# [AGENTS.md](http://AGENTS.md)

Agent instructions for modern Laravel projects. This file is the entrypoint: read it first, then load the focused guideline file relevant to your current task.

**Target stack:** Laravel 13 · PHP 8.3+ · Livewire 3 (+ Volt, Alpine) · Blade · Pest 3 · Pint · Larastan

## Project ethos

Write clean, scalable, boring Laravel. Favor framework conventions over clever abstractions. Every feature is validated, authorized, and tested. Optimize for the next developer who reads the code, not for showing off.

## Golden rules

1. **Convention over configuration.** Use Laravel's defaults and directory structure. Don't invent bespoke architecture without a concrete reason.
2. **Thin controllers, thin models.** Controllers coordinate; models persist. Business logic lives in Actions/Services.
3. **Never trust input.** Validate with Form Requests / Livewire validation, authorize with Policies, on every mutating action.
4. **Type everything.** `declare(strict_types=1)`, typed properties, params, and return types.
5. **No N+1 queries.** Eager load relationships; enable `preventLazyLoading()` in non-production.
6. **Test behavior, not implementation.** Every feature ships with Pest tests.
7. **Let tooling decide style.** Pint owns formatting; Larastan owns static correctness. Run both before you consider work done.
8. **Don't over-engineer.** No repositories over Eloquent, no interfaces with one implementation, no premature patterns.



## Guideline index

Load the file(s) matching your task:

- [Architecture & structure](docs/agent-guidelines/01-architecture.md) — directory layout, Actions vs Services, DTOs, enums, layering.
- [Eloquent & database](docs/agent-guidelines/02-eloquent-database.md) — models, migrations, relationships, N+1, query patterns.
- [HTTP & validation](docs/agent-guidelines/03-http-validation.md) — routing, thin controllers, Form Requests, API resources.
- [Livewire & Blade](docs/agent-guidelines/04-livewire-blade.md) — Livewire 3, Volt, Form Objects, Blade components, Alpine.
- [Testing with Pest](docs/agent-guidelines/05-testing-pest.md) — Pest style, feature/unit tests, factories, datasets.
- [Code style & static analysis](docs/agent-guidelines/06-code-style-static-analysis.md) — strict types, PHP 8.x features, Pint, Larastan.
- [Security](docs/agent-guidelines/07-security.md) — authorization, mass assignment, output escaping, secrets.
- [Performance & queues](docs/agent-guidelines/08-performance-queues.md) — caching, queued jobs, eager loading, pagination.

### Design

- [Design language](docs/design/design-language.md) — Industrial Dark tokens, typography, components, and interaction rules. Load this for any UI, Blade, or styling work.
- [UI specification](docs/design/ui-spec/README.md) — Carport Monitor screen layouts and per-feature design (Figma Make). Load alongside the design language when implementing views.

## Workflow expectations

- Before finishing any change: run `./vendor/bin/pint`, `./vendor/bin/phpstan analyse` (Larastan), and `./vendor/bin/pest`.
- Keep changes small and focused; match the surrounding code's conventions.
- Prefer editing existing files over adding new ones. Don't create documentation unless asked.
- When a guideline conflicts with existing project code, follow the existing project convention and flag the discrepancy.

## Git commit format

Prefix every commit message with a type in square brackets, followed by a short description:

```
[type] Short description of the change
```

Example: `[feat] Add vehicle detail screen`

### Types

| Type | Use for |
|------|---------|
| `feat` | Feature |
| `fix` | Bugfix |
| `style` | Styling |
| `refrac` | Verbesserung des Codes |
| `test` | Automatisierte Tests |
| `docs` | Dokumentation |
| `project` | Änderungen der Projektkonfiguration |
| `perf` | Verbesserun der Performance |
| `wip` | Work in Progress / Zwischenstände |

