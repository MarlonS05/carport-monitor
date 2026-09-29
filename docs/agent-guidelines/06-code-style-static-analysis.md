# Code Style & Static Analysis

Back to [AGENTS.md](../../AGENTS.md).

Let tooling own style and correctness so reviews focus on design. Pint formats; Larastan checks types.

## Types are mandatory

- Start every PHP file with `declare(strict_types=1);`.
- Type all parameters, return types, and properties. Use `void` and `never` where they apply.
- Use constructor property promotion and `readonly` for immutable data.

```php
// GOOD
declare(strict_types=1);

final class Money
{
    public function __construct(
        public readonly int $amount,
        public readonly Currency $currency,
    ) {}

    public function add(self $other): self
    {
        return new self($this->amount + $other->amount, $this->currency);
    }
}
```

```php
// BAD: no strict types, untyped params, mutable public state
class Money {
    public $amount;
    function add($other) { ... }
}
```

## Use modern PHP 8.x features

- **Enums** for fixed sets (see [Architecture](01-architecture.md)).
- **`match`** over `switch` for expression-style branching.
- **Named arguments** for calls with many/optional params.
- **Nullsafe** `?->` instead of nested null checks.
- **First-class callable syntax** `$fn(...)` where it reads well.
- **`readonly` classes/properties** for value objects and DTOs.

```php
// GOOD
$label = match ($order->status) {
    OrderStatus::Pending => 'Awaiting payment',
    OrderStatus::Completed => 'Done',
    OrderStatus::Cancelled => 'Cancelled',
};
```

## Naming

- Classes: `StudlyCase` (`CreateOrder`, `OrderStatus`).
- Methods & variables: `camelCase`.
- Database columns: `snake_case`; table names plural `snake_case`.
- Booleans read as predicates: `isActive`, `hasItems`, `canPublish`.
- Be explicit; avoid abbreviations that aren't universal.

## Pint (formatting)

- Pint (Laravel preset) is the single source of truth for formatting. Do not hand-format or argue style in review.
- Run before finishing: `./vendor/bin/pint`.
- Keep any preset overrides in `pint.json`; don't fight the formatter with inline exceptions.

## Larastan / PHPStan (static analysis)

- Run `./vendor/bin/phpstan analyse`. Target a high level (aim for level 6+, raise over time).
- Fix reported issues rather than suppressing them. Only use `@phpstan-ignore` with a short justifying comment when a false positive is unavoidable.
- Manage legacy debt with a baseline (`phpstan.neon`), and don't add new entries to it.

## General hygiene

- `final` by default for classes not designed for extension.
- Small functions, early returns, minimal nesting.
- No dead code, commented-out blocks, or leftover `dd()`/`dump()`/`ray()`.
- Comments explain *why*, not *what*. Don't narrate obvious code.
- Use dependency injection over facades in classes you unit-test; facades are fine in controllers/quick contexts.
