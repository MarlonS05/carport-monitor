# Security

Back to [AGENTS.md](../../AGENTS.md).

Security is not optional and not a later step. Validate, authorize, and escape by default.

## Authorization

- Every mutating action (controller, Livewire, job triggered by a user) checks authorization via a Policy or Gate.
- Keep authorization logic in Policies (`app/Policies`), not scattered `if` checks.

```php
// GOOD — controller / Form Request
$this->authorize('update', $order);

// GOOD — Livewire component action
public function delete(Order $order): void
{
    $this->authorize('delete', $order);
    $order->delete();
}

// GOOD — Blade
@can('update', $order)
    <a href="{{ route('orders.edit', $order) }}">Edit</a>
@endcan
```

```php
// BAD: trusting the request / no ownership check
$order = Order::find(request('id'));
$order->delete();
```

## Never trust input

- Validate all input with Form Requests / Livewire validation before use.
- Rely on mass-assignment protection: whitelist with `$fillable`, never blanket `$guarded = []` (see [Eloquent](02-eloquent-database.md)).
- Don't build queries from raw request data; use bindings and validated values.
- Scope queries to the authenticated user where relevant: `$request->user()->orders()->findOrFail($id)`.

## Output escaping

- Blade auto-escapes `{{ $value }}` — rely on it.
- `{!! $html !!}` outputs raw HTML. Only use it for content you have explicitly sanitized (e.g. via an HTML purifier). Never render raw user input.

```blade
{{-- BAD: XSS if $bio contains markup --}}
{!! $user->bio !!}

{{-- GOOD --}}
{{ $user->bio }}
```

## Secrets & configuration

- Secrets live in `.env` (never committed) and are read through `config()`.
- Call `env()` only inside `config/*.php` files. Elsewhere use `config('services.stripe.key')` — otherwise `config:cache` breaks in production.
- Never log secrets, tokens, or full request payloads containing credentials.

```php
// BAD: env() at runtime — returns null once config is cached
$key = env('STRIPE_SECRET');

// GOOD
$key = config('services.stripe.secret');
```

## Framework protections

- **CSRF:** keep the `@csrf` directive on forms; don't disable CSRF middleware casually. Livewire handles this automatically.
- **Rate limiting:** apply `throttle` middleware / `RateLimiter` to auth, and sensitive or expensive endpoints.
- **Signed URLs:** use `URL::signedRoute()` / `hasValidSignature()` for tamper-proof links (email confirmations, unsubscribes).
- **Encrypted casts:** use the `encrypted` cast for sensitive columns at rest.
- **Password/hashing:** use `Hash::make()` / `Hash::check()`; never store or compare plaintext.

```php
protected function casts(): array
{
    return ['api_token' => 'encrypted'];
}
```

## Data exposure

- Hide sensitive attributes with `$hidden` and shape API output with Resources (see [HTTP & Validation](03-http-validation.md)).
- Return generic error messages to users; log details server-side. Never leak stack traces in production (`APP_DEBUG=false`).
- Prefer `findOrFail()` (404) over leaking whether a record exists to unauthorized users.
