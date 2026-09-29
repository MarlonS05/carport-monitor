# Architecture & Structure

Back to [AGENTS.md](../../AGENTS.md).

Keep the architecture flat and Laravel-idiomatic. Add layers only when complexity demands it.

## Directory conventions

Use Laravel's defaults. Group domain logic under intention-revealing folders:

```text
app/
  Actions/            # Single-purpose use cases (business logic)
  Data/               # DTOs (spatie/laravel-data or plain readonly classes)
  Enums/              # Backed enums for statuses, types, roles
  Http/
    Controllers/      # Thin: coordinate request -> action -> response
    Requests/         # Form Requests: validation + authorization
    Middleware/
  Livewire/           # Livewire components (+ Forms/ for Form Objects)
  Models/
  Policies/
  Support/            # Small framework-agnostic helpers
```

## Where logic belongs

- **Controllers / Livewire components** coordinate. They receive validated input, call one Action, return a response/redirect. No business rules inline.
- **Actions** hold a single use case (`CreateInvoice`, `PublishPost`). Reusable from controllers, Livewire, jobs, and commands.
- **Models** describe persistence and relationships. Keep them free of heavy business logic.
- **Services** are for stateful/coordinating concerns that span multiple actions (e.g. a payment gateway wrapper). Prefer an Action first; reach for a Service only when it earns its place.

## Action classes

One public method, dependencies injected via the constructor.

```php
// GOOD
final class CompleteOrder
{
    public function __construct(private readonly InventoryService $inventory) {}

    public function handle(Order $order): Order
    {
        $this->inventory->reserve($order);

        $order->update(['status' => OrderStatus::Completed]);

        OrderCompleted::dispatch($order);

        return $order;
    }
}
```

```php
// BAD: business logic stuffed into the controller
public function store(Request $request)
{
    // validation, inventory checks, status mutation, event firing all inline...
}
```

## DTOs over arrays

Pass structured, typed data across boundaries instead of loose arrays.

```php
// GOOD
final readonly class CustomerData
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $company = null,
    ) {}
}
```

Use `spatie/laravel-data` if the project already includes it; otherwise plain `readonly` classes are fine. Avoid `array $data` parameters for domain concepts.

## Enums over magic values

```php
// GOOD
enum OrderStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function isFinal(): bool
    {
        return in_array($this, [self::Completed, self::Cancelled], true);
    }
}
```

Cast them on models (`protected function casts(): array`). Never scatter raw strings like `'completed'` through the codebase.

## Avoid over-engineering

- No Repository pattern wrapping Eloquent — Eloquent already is the data layer.
- No interface for a class with a single implementation, unless it's a genuine seam (external API, swappable driver).
- No premature event/queue indirection for simple synchronous work.
- Reach for a pattern only when the duplication or complexity is real and present, not hypothetical.
