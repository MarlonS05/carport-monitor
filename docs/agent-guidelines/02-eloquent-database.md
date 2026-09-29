# Eloquent & Database

Back to [AGENTS.md](../../AGENTS.md).

## Model conventions

- Use `$fillable` to whitelist mass-assignable attributes. Never use a blanket `protected $guarded = [];`.
- Declare casts via the `casts()` method (Laravel 11+), not the `$casts` property.
- Type accessors/mutators with the `Attribute` class.

```php
// GOOD
final class Order extends Model
{
    protected $fillable = ['customer_id', 'status', 'total'];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'total' => 'decimal:2',
            'placed_at' => 'immutable_datetime',
        ];
    }

    protected function reference(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => strtoupper($value),
        );
    }
}
```

```php
// BAD: everything mass-assignable, stringly-typed status, array casts property
protected $guarded = [];
protected $casts = ['status' => 'string'];
```

## Relationships

- Type every relationship's return (`HasMany`, `BelongsTo`, ...).
- Name them conventionally so Laravel infers keys; be explicit only when they differ.

```php
public function customer(): BelongsTo
{
    return $this->belongsTo(Customer::class);
}
```

## Prevent N+1 queries

- Eager load what you'll use: `Order::with('customer', 'items')->get()`.
- Use `loadMissing()` when a model may already have the relation loaded.
- Enable strict mode in a service provider `boot()` for non-production:

```php
Model::preventLazyLoading(! app()->isProduction());
Model::preventSilentlyDiscardingAttributes(! app()->isProduction());
```

```php
// BAD: N+1 — one query per order to fetch the customer
foreach (Order::all() as $order) {
    echo $order->customer->name;
}

// GOOD
foreach (Order::with('customer')->get() as $order) {
    echo $order->customer->name;
}
```

## Migrations

- Descriptive names; keep them reversible (avoid `down()` that loses intent).
- Use `foreignId()->constrained()` and index foreign/lookup columns.
- Prefer `->cascadeOnDelete()` / `->restrictOnDelete()` explicitly.

```php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
    $table->string('status')->index();
    $table->decimal('total', 10, 2);
    $table->timestamp('placed_at')->nullable();
    $table->timestamps();
});
```

## Queries

- Extract reusable constraints into query scopes:

```php
public function scopeCompleted(Builder $query): void
{
    $query->where('status', OrderStatus::Completed);
}
```

- Push filtering/aggregation into the database; don't fetch collections to filter in PHP.
- Avoid raw SQL unless necessary; when unavoidable, use bindings (`whereRaw('... = ?', [$value])`), never string interpolation.
- For large datasets use `chunkById()`, `cursor()`, or `lazy()` to bound memory.

```php
// GOOD: bounded memory over millions of rows
Order::completed()->lazyById()->each(fn (Order $order) => $order->archive());
```

## Transactions

Wrap multi-write operations in `DB::transaction()` so partial failures roll back.

```php
DB::transaction(function () use ($order) {
    $order->save();
    $order->items()->createMany($items);
});
```
