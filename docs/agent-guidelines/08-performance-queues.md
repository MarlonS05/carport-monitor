# Performance & Queues

Back to [AGENTS.md](../../AGENTS.md).

Make the common path fast: fewer queries, cached reads, and slow work moved off the request cycle. Measure before optimizing.

## Database access first

The biggest wins are usually query-related (see [Eloquent & Database](02-eloquent-database.md)):

- Eliminate N+1 with eager loading; keep `preventLazyLoading()` on in dev.
- Select only needed columns for large reads (`->select([...])`).
- Add indexes for columns used in `where`, `orderBy`, and joins.
- Stream large result sets with `cursor()` / `lazyById()` instead of `get()`.

## Pagination

Never load unbounded collections into a view. Paginate lists.

```php
// GOOD
$orders = Order::with('customer')->latest()->paginate(25);

// BAD: loads every row into memory
$orders = Order::all();
```

Use `simplePaginate()` when you don't need total counts, and `cursorPaginate()` for large, frequently-changing datasets.

## Caching

- Cache expensive, read-heavy, or rarely-changing computations.
- Use a clear key strategy and remember to invalidate on writes.

```php
// GOOD
$stats = Cache::remember("customer:{$customer->id}:stats", now()->addHour(), function () use ($customer) {
    return $customer->orders()->completed()->sum('total');
});

// Invalidate when the underlying data changes
Cache::forget("customer:{$customer->id}:stats");
```

- Use cache tags (Redis/Memcached) to invalidate groups when the driver supports it.
- Don't cache per-user sensitive data under shared keys.

## Queues & jobs

Move slow or external work off the request: email, notifications, third-party API calls, report generation, image processing.

```php
// GOOD
SendOrderConfirmation::dispatch($order)->afterCommit();
```

Job guidelines:

- **Idempotent:** safe to run more than once (retries happen).
- **Small payloads:** queue IDs, not full models — use `SerializesModels` / re-fetch inside the job.
- **Resilient:** set `$tries`, `$backoff`, and `$timeout`; implement `failed()` for cleanup/alerting.
- Use `->afterCommit()` so jobs don't run before the transaction that created their data commits.
- Queue event listeners (`ShouldQueue`) for side effects instead of doing them inline.

```php
final class SendOrderConfirmation implements ShouldQueue
{
    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function __construct(public int $orderId) {}

    public function handle(): void
    {
        $order = Order::findOrFail($this->orderId);
        // ... send mail
    }

    public function failed(Throwable $e): void
    {
        // log / alert
    }
}
```

## Deferred work

For lightweight side effects that don't need a queue worker, use `defer()` to run them after the response is sent.

```php
defer(fn () => Analytics::track('order.viewed', $order->id));
```

## Production hygiene

- Cache framework state in deploys: `config:cache`, `route:cache`, `event:cache`, `view:cache` (this is why `env()` must not be used at runtime — see [Security](07-security.md)).
- Use a real queue driver (Redis/SQS) and run workers/Horizon in production; don't rely on `sync`.
- Profile with tools (Debugbar/Telescope in dev, Pulse in prod) before optimizing — avoid guesswork.
