# Livewire & Blade

Back to [AGENTS.md](../../AGENTS.md).

Livewire 3 is the primary interactivity layer. Blade renders everything; Alpine handles light client-side behavior. **Server-side validation and authorization are mandatory in every component action** — the client is never trusted.

## Livewire component conventions

- Type all public properties. They are the component's state and are serialized to the client.
- Validate with `#[Validate]` attributes or a Form Object; never skip validation.
- Use `#[Computed]` for derived values so they aren't stored in state or recomputed in the view.
- Authorize inside actions with `$this->authorize(...)`.

```php
// GOOD
final class EditOrder extends Component
{
    public Order $order;

    #[Validate('required|string|max:255')]
    public string $note = '';

    public function mount(Order $order): void
    {
        $this->authorize('update', $order);
        $this->order = $order;
    }

    #[Computed]
    public function total(): string
    {
        return $this->order->items->sum('price');
    }

    public function save(UpdateOrder $action): void
    {
        $this->authorize('update', $this->order);
        $this->validate();

        $action->handle($this->order, $this->note);

        $this->dispatch('order-saved');
    }

    public function render(): View
    {
        return view('livewire.edit-order');
    }
}
```

- Delegate real work to an Action (see [Architecture](01-architecture.md)); the component only orchestrates.

## Form Objects for complex forms

Extract multi-field forms into a Form Object to keep components lean.

```php
final class OrderForm extends Form
{
    #[Validate('required|exists:customers,id')]
    public ?int $customer_id = null;

    #[Validate('required|array|min:1')]
    public array $items = [];

    public function store(CreateOrder $action): Order
    {
        $this->validate();

        return $action->handle(OrderData::from($this->all()));
    }
}
```

## Data binding

Bind intentionally — avoid re-rendering on every keystroke unless you need it.

- `wire:model` (deferred, default in v3) for most inputs.
- `wire:model.blur` to sync when the field loses focus.
- `wire:model.live` only when live feedback (search, validation preview) is genuinely required.
- Use `wire:key` on items in loops so Livewire tracks DOM correctly.

```blade
{{-- GOOD: deferred by default, live only where needed --}}
<input type="text" wire:model="note">
<input type="search" wire:model.live.debounce.300ms="query">
```

## Volt

Volt (functional single-file components) is fine for small, self-contained components. Use full class components when logic grows, when you need testable methods in isolation, or for consistency in a class-based codebase. Pick one style per feature area and stay consistent.

## Blade

- **Components over includes.** Use `<x-...>` components with `@props` and typed slots for reusable UI. Reserve `@include` for trivial partials.
- Let Blade auto-escape (`{{ $value }}`). Only use `{!! !!}` for content you have explicitly sanitized (see [Security](07-security.md)).
- Merge/forward attributes with `$attributes` so components stay flexible.

```blade
{{-- resources/views/components/alert.blade.php --}}
@props(['type' => 'info'])

<div {{ $attributes->class(['alert', "alert-{$type}"]) }}>
    {{ $slot }}
</div>
```

## Alpine

- Use Alpine for purely client-side UI state (toggles, dropdowns, modals) that needs no server round-trip.
- Don't duplicate server state in Alpine; if it must persist or be validated, it belongs in Livewire.
- Use `$wire` to bridge into Livewire from Alpine when needed, rather than reimplementing logic.

```blade
<div x-data="{ open: false }">
    <button @click="open = !open">Details</button>
    <div x-show="open" x-collapse>...</div>
</div>
```
