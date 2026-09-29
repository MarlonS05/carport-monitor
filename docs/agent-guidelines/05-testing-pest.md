# Testing with Pest

Back to [AGENTS.md](../../AGENTS.md).

Every feature ships with tests. Test observable behavior, not internal implementation. Use Pest 3.

## Style

- Use `it()` / `test()` with descriptive names and the expectation API.
- Follow arrange–act–assert; one behavior per test.
- Share setup with `beforeEach()`; reduce repetition with datasets.

```php
it('creates an order for the customer', function () {
    $customer = Customer::factory()->create();

    $order = app(CreateOrder::class)->handle(new OrderData(
        customerId: $customer->id,
        items: [['sku' => 'ABC', 'quantity' => 2]],
    ));

    expect($order->status)->toBe(OrderStatus::Pending)
        ->and($order->items)->toHaveCount(1);
});
```

## Test types

- **Feature tests** for HTTP endpoints and full flows (`get`, `post`, asserting responses/DB state).
- **Livewire tests** with `Livewire::test()` for component behavior.
- **Unit tests** for Actions, DTOs, and pure logic in isolation.

```php
// HTTP feature test
it('requires authentication to create orders', function () {
    $this->post(route('orders.store'), [])->assertRedirect(route('login'));
});

// Livewire component test
use function Livewire\Volt\test; // or Livewire\Livewire::test for class components

it('saves a note on the order', function () {
    $order = Order::factory()->create();

    Livewire::test(EditOrder::class, ['order' => $order])
        ->set('note', 'Handle with care')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('order-saved');

    expect($order->fresh()->note)->toBe('Handle with care');
});
```

## Factories & database

- Every model has a factory; build test data with factories, never hand-rolled inserts.
- Use `RefreshDatabase` (configured in `Pest.php` via `uses()`).
- Use factory states and relationships to express intent.

```php
// tests/Pest.php
uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class)->in('Feature');

// In a test
$order = Order::factory()->completed()->for($customer)->has(Item::factory()->count(3))->create();
```

## Datasets

Cover multiple inputs without duplicating tests.

```php
it('rejects invalid quantities', function (int $quantity) {
    Livewire::test(EditOrder::class)
        ->set('items.0.quantity', $quantity)
        ->call('save')
        ->assertHasErrors('items.0.quantity');
})->with([0, -1, -100]);
```

## Assertions worth using

- Validation: `assertHasErrors` / `assertHasNoErrors`.
- Authorization: `assertForbidden`, `assertStatus(403)`.
- Database: `assertDatabaseHas`, `assertDatabaseCount`, `assertModelExists`.
- Side effects: fake and assert — `Event::fake()`, `Queue::fake()`, `Mail::fake()`, `Notification::fake()`, `Bus::fake()`.

## Philosophy

- Aim for meaningful coverage of behavior and edge cases, not a 100% coverage number.
- A bug fix starts with a failing test that reproduces it.
- Keep tests fast and deterministic; avoid real network/time dependencies (freeze time with `travel()`/`Carbon::setTestNow()`).
