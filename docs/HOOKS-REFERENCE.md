# Hooks Reference

All actions and filters registered by this plugin.

## Actions

### `init`

| Hook | Class | Method | Description |
|---|---|---|---|
| `add_action('init', ...)` | `PostTypeProvider` | `registerPostTypes` | Registers the `portfolio`, `testimonial`, and `faq` CPTs |
| `add_action('init', ...)` | `TaxonomyProvider` | `registerTaxonomies` | Registers the `skill` and `industry` taxonomies |
| `add_action('init', ...)` | `RewriteRules` | `add_rewrite_rules` | Adds `/portfolio/category/{slug}/` rewrite rule |
| `add_action('init', ...)` | `ImageService` | `register_image_sizes` | Registers `portfolio-thumb`, `portfolio-hero`, `portfolio-card` sizes |

### `rest_api_init`

| Hook | Class | Method | Description |
|---|---|---|---|
| `add_action('rest_api_init', ...)` | `RestApiProvider` | `registerControllers` | Registers all REST controllers |

### `admin_menu`

| Hook | Class | Method | Description |
|---|---|---|---|
| `add_action('admin_menu', ...)` | `AdminProvider` | `registerMenuPages` | Registers plugin admin pages under Settings |

### `admin_init`

| Hook | Class | Method | Description |
|---|---|---|---|
| `add_action('admin_init', ...)` | `AdminProvider` | `registerSettings` | Registers settings sections and fields via Settings API |

### `wp_ajax_*`

| Hook | Class | Description |
|---|---|---|
| `wp_ajax_wsp_get_stats` | `AjaxProvider` | Returns portfolio statistics for authenticated users |
| `wp_ajax_nopriv_wsp_get_stats` | `AjaxProvider` | Returns portfolio statistics for all visitors |

### `wp_starter_cleanup` (cron)

| Hook | Class | Description |
|---|---|---|
| `wp_starter_cleanup` | `CronProvider` | Purges expired transients and cleans up stale data |

### `pre_get_posts`

| Hook | Class | Method | Description |
|---|---|---|---|
| `add_action('pre_get_posts', ...)` | `QueryModifiers` | `modify_portfolio_archive` | Sets 12 posts per page on portfolio archives |

### `wp_handle_upload`

| Hook | Class | Method | Description |
|---|---|---|---|
| `add_filter('wp_handle_upload', ...)` | `ImageService` | `handle_upload` | Post-upload hook (extensible for WebP conversion) |

## Filters

### `the_content`

| Hook | Class | Method | Description |
|---|---|---|---|
| `add_filter('the_content', ...)` | `ContentFilters` | `append_portfolio_link` | Appends "Back to Portfolio" link on single portfolio posts |

### `excerpt_length`

| Hook | Class | Method | Description |
|---|---|---|---|
| `add_filter('excerpt_length', ...)` | `ContentFilters` | `set_excerpt_length` | Sets excerpt length to 25 words |

### `body_class`

| Hook | Class | Method | Description |
|---|---|---|---|
| `add_filter('body_class', ...)` | `ContentFilters` | `add_body_classes` | Adds `is-portfolio-single` and `is-portfolio-archive` classes |

### `query_vars`

| Hook | Class | Method | Description |
|---|---|---|---|
| `add_filter('query_vars', ...)` | `RewriteRules` | `add_query_vars` | Registers the `skill` query variable |

## Shortcodes

| Shortcode | Class | Description |
|---|---|---|
| `[portfolio_list]` | `ShortcodeProvider` | Renders a portfolio grid with optional `count` and `featured` attributes |
| `[testimonials]` | `ShortcodeProvider` | Renders a testimonial carousel |
