# WP-CLI Commands

All commands are registered under the `starter` top-level command group.

## wp starter seed

Seed the database with dummy portfolio items for development and testing.

### Synopsis

```bash
wp starter seed [--count=<count>]
```

### Options

| Option | Type | Default | Description |
|---|---|---|---|
| `--count=<count>` | integer | 10 | Number of portfolio items to create |

### Examples

```bash
# Create 10 items (default)
wp starter seed

# Create 50 items
wp starter seed --count=50

# Create 1 item for a quick smoke test
wp starter seed --count=1
```

### Notes

- Items are created with `post_status = publish` and `post_type = portfolio`.
- Titles are sequential: "Portfolio Item 1", "Portfolio Item 2", etc.
- A WP-CLI progress bar is displayed during creation.

---

## wp starter cache flush

Flush all plugin transients from the `wp_options` table.

### Synopsis

```bash
wp starter cache flush
```

### Examples

```bash
wp starter cache flush
# Success: Flushed 14 plugin transients.
```

### Notes

- Deletes all rows in `wp_options` where `option_name` matches `_transient_wsp_%`.
- Safe to run in production; cached data will be regenerated on next request.

---

## wp starter cache stats

Display a count of active plugin transients.

### Synopsis

```bash
wp starter cache stats
```

### Examples

```bash
wp starter cache stats
# Active plugin transients: 8
```

---

## Registration

Commands are registered in `Plugin::boot()` when the `WP_CLI` constant is defined:

```php
if ( defined( 'WP_CLI' ) && \WP_CLI ) {
    \WP_CLI::add_command( 'starter', CLI\SeedCommand::class );
    \WP_CLI::add_command( 'starter cache', CLI\CacheCommand::class );
}
```
