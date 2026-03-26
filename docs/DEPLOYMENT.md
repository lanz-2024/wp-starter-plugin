# Deployment

## Local Development (LocalWP)

1. Clone the repo
2. Symlink the plugin:
```bash
ln -s /path/to/wp-starter-plugin /path/to/wordpress/wp-content/plugins/wp-starter-plugin
```
3. Activate via WP Admin → Plugins, or:
```bash
wp plugin activate wp-starter-plugin
```

**Local test environment:**
- Path: `/Users/lan/Local Sites/alan-projects/app/public`
- WP version: 6.9.4, PHP: 8.5.4

## Production

```bash
# Zip for upload
zip -r wp-starter-plugin.zip wp-starter-plugin/ --exclude "*.git*" --exclude "vendor/*" --exclude "node_modules/*" --exclude "tests/*"

# Or via WP-CLI
wp plugin install wp-starter-plugin.zip --activate
```

## Building Gutenberg Blocks

```bash
pnpm install           # Install block dependencies
pnpm build             # Build block assets (@wordpress/scripts)
pnpm dev               # Watch mode
```

## Seeding Demo Data

```bash
# Seed 50 Portfolio + Testimonial + FAQ entries
wp starter seed --count=50

# Or individually
wp starter seed --type=portfolio --count=20
wp starter seed --type=testimonial --count=15
wp starter seed --type=faq --count=10
```

## WP-CLI Commands

```bash
wp starter seed          # Seed demo content
wp starter cache warm    # Warm transient caches
wp starter cache flush   # Clear all plugin caches
wp starter export        # Export CPT data as CSV/JSON
wp starter migrate       # Run database migrations
```

## Requirements

- WordPress 6.8+, PHP 8.3+
- Composer: `composer install` for autoloader + dev deps
