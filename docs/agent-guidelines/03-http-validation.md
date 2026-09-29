# HTTP & Validation

Back to [AGENTS.md](../../AGENTS.md).

Applies to traditional controllers and any JSON endpoints. For Livewire UIs, see [Livewire & Blade](04-livewire-blade.md) — the validation and authorization principles are the same.

## Routing

- Name routes and group by concern/middleware.
- Use route–model binding; let Laravel resolve models from the URL.
- Prefer resourceful controllers (`Route::resource`) when the CRUD shape fits.

```php
// GOOD
Route::middleware('auth')->group(function () {
    Route::resource('orders', OrderController::class);
});

// Route-model binding resolves {order} to an Order instance
public function show(Order $order): View
{
    return view('orders.show', ['order' => $order]);
}
```

## Thin controllers

A controller action validates (via Form Request), delegates to one Action, and returns a response. No business logic, no heavy queries inline.

```php
// GOOD
public function store(StoreOrderRequest $request, CreateOrder $action): RedirectResponse
{
    $order = $action->handle(OrderData::from($request->validated()));

    return to_route('orders.show', $order)->with('status', 'Order created.');
}
```

```php
// BAD: validation + logic + persistence all in the controller
public function store(Request $request)
{
    $request->validate([...]);
    // inventory checks, totals, saving, event dispatch...
}
```

## Form Requests

Put validation and authorization in a Form Request, never inline in the controller.

```php
final class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Order::class);
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.sku' => ['required', 'string'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
```

- Use array syntax for rules (not pipe strings) — it's readable and composable.
- Prefer Rule objects (`Rule::enum()`, `Rule::unique()`) over stringly-typed rules.
- Add `prepareForValidation()` for normalization and `messages()` for custom copy.

## API resources

For JSON output, shape responses with API Resources instead of returning models directly.

```php
final class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'total' => $this->total,
            'customer' => CustomerResource::make($this->whenLoaded('customer')),
        ];
    }
}
```

- Use `whenLoaded()` to avoid triggering N+1 from the serializer.
- Keep API responses stable and versioned; don't leak internal column names blindly.

## Responses

- Type action return values (`RedirectResponse`, `JsonResponse`, `View`).
- Redirect with `to_route()` and flash a status message after mutations (POST/PUT/DELETE).
- Return correct status codes (`201` on create, `204` on delete) for APIs.
