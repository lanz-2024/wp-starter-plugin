# Security

## WordPress Security Standards

All plugin code follows WordPress security best practices at the highest level.

### Nonce Verification
- ALL form submissions verified with `wp_verify_nonce()` or `check_ajax_referer()`
- ALL admin POST handlers check nonces before processing
- REST API endpoints use `permission_callback` with proper capability checks

### Input Sanitization
- All user input sanitized: `sanitize_text_field()`, `sanitize_textarea_field()`, `absint()`, `wp_kses_post()`
- No raw `$_POST`/`$_GET` access — always through sanitization wrappers
- Settings API fields use `sanitize_callback` parameter

### Output Escaping
- All output escaped: `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses()`, `wp_kses_post()`
- No raw `echo` of database content or user-supplied data

### Database Queries
- All custom queries use `$wpdb->prepare()` — no string interpolation in SQL
- Drizzle-style type-safe queries where applicable
- No raw SQL with user input

### Capability Checks
- Every admin action gated with `current_user_can()` before execution
- REST API `permission_callback` returns `false` for unauthorized requests (not `WP_Error`)
- AJAX handlers check capabilities AND nonces

## PSR-11 DI Container

- Services registered with interface bindings — loose coupling, testable
- No service locator pattern — all dependencies injected
- Container initialized once on plugin load — no global state mutations

## PHPCS / PHPStan

- PHPCS: WordPress-Extra + WordPress-Docs — zero errors in CI
- PHPStan: level 8 with WordPress stubs + szepeviktor/phpstan-wordpress — zero errors

## Uninstall Cleanup

`uninstall.php` removes all plugin data on uninstall:
- Custom options (`get_option`/`delete_option`)
- Custom database tables (if any)
- Post meta for custom post types
- Scheduled cron events
