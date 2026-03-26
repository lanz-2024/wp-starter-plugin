# Architecture

## Overview

WP Starter Plugin is structured around a PSR-11 compatible dependency injection container. The main plugin file boots a `Plugin` instance which builds the container, registers service bindings, and iterates over service providers.

## PSR-4 Namespace Layout

```
WPStarterPlugin\
  CLI\           — WP-CLI commands (SeedCommand, CacheCommand)
  Hooks\         — WordPress action/filter classes (ContentFilters, QueryModifiers, RewriteRules)
  Models\        — Typed data models (Portfolio, Settings)
  PostTypes\     — CPT registration classes (Portfolio, Testimonial, FAQ)
  Providers\     — Service providers bootstrapped on plugins_loaded
  Rest\          — REST API controllers (AbstractController, PortfolioController, ...)
  Services\      — Business logic services (CacheManager, QueryOptimizer, ExportService, ...)
  Traits\        — Shared trait behaviour (HasHooks, HasMeta)
```

## DI Container

`Container` implements `Psr\Container\ContainerInterface` and supports:

- `bind(string $abstract, Closure $factory)` — transient factory; a new instance per `make()` call.
- `singleton(string $abstract, Closure $factory)` — factory called once; same instance returned thereafter.
- `make(string $abstract)` — resolves a binding or falls back to reflection-based auto-wiring.
- `get(string $id)` / `has(string $id)` — PSR-11 contract methods.

Auto-wiring inspects a class constructor via `ReflectionClass` and recursively resolves type-hinted parameters from the container.

## Service Providers

Each provider implements a `register(): void` method that attaches WordPress hooks. Providers are instantiated via the container so their own dependencies are auto-wired.

| Provider | Responsibility |
|---|---|
| `PostTypeProvider` | Registers CPTs on `init` |
| `TaxonomyProvider` | Registers custom taxonomies on `init` |
| `RestApiProvider` | Hooks REST controllers onto `rest_api_init` |
| `AdminProvider` | Admin pages, settings, meta boxes |
| `AjaxProvider` | `wp_ajax_*` handlers |
| `CronProvider` | Scheduled event registration |
| `ShortcodeProvider` | `add_shortcode` registrations |
| `BlockProvider` | Gutenberg block type registration |

## Custom Post Types

Three CPTs are registered:

- `portfolio` — Showcases projects. Supports: title, editor, thumbnail, excerpt, custom-fields.
- `testimonial` — Client quotes. Supports: title, editor, thumbnail.
- `faq` — Frequently asked questions. Supports: title, editor.

Each CPT class carries a `POST_TYPE` constant and meta helper methods provided by the `HasMeta` trait.

## REST API

All controllers extend `AbstractController` which sets `$namespace = 'wp-starter/v1'` and provides shared `canRead()`, `canEdit()`, and `error()` helpers.

The `PortfolioController` exposes full CRUD:

```
GET    /wp-json/wp-starter/v1/portfolio
POST   /wp-json/wp-starter/v1/portfolio
GET    /wp-json/wp-starter/v1/portfolio/{id}
PUT    /wp-json/wp-starter/v1/portfolio/{id}
DELETE /wp-json/wp-starter/v1/portfolio/{id}
```

Responses are cached via `CacheManager` transients keyed by query parameters.

## Gutenberg Blocks

Block types are registered server-side through `BlockProvider` using `register_block_type()` with a `block.json` manifest. Dynamic blocks use `ServerSideRender` and return markup from a PHP render callback.

## Caching Strategy

`CacheManager` wraps WordPress transients under a `wsp_` prefix. `QueryOptimizer` uses the cache layer to avoid repeated `WP_Query` calls for identical argument sets.
