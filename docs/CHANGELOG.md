# Changelog

## [0.1.0] - 2026-03-27

### Added
- PSR-11 DI container with lazy loading and interface binding
- PSR-4 autoloader under `WPStarterPlugin\` namespace
- Custom Post Types: Portfolio, Testimonial, FAQ (full registration with all args)
- Custom Taxonomies: Skill (flat), Industry (hierarchical)
- Full Settings API: panels, sections, fields with sanitization callbacks
- WP REST API: PortfolioController, SettingsController, StatsController (full CRUD + schema)
- Meta boxes with nonce verification (Portfolio, Testimonial)
- Sortable custom admin columns for all CPTs
- Custom dashboard widget with live stats
- Dismissible admin notices
- WP_Query integration: pre_get_posts, posts_clauses, meta queries, tax queries
- AJAX handlers: load-more, filter, sort (wp_ajax_ + wp_ajax_nopriv_)
- WP Cron: scheduled cleanup and sync jobs
- Shortcodes: [portfolio], [testimonials], [faq] (enclosing + self-closing)
- Gutenberg blocks: PortfolioGrid (dynamic), TestimonialSlider (hybrid), FAQAccordion (Interactivity API)
- WP-CLI commands: seed, cache (warm/flush/stats), export, migrate
- Transient caching + object cache awareness (CacheManager)
- QueryOptimizer: N+1 detection, query logging
- ImageService: WebP conversion on upload, custom sizes, srcset
- EmailService: wp_mail HTML templates + queue
- ExportService: CSV/JSON export for CPT data
- ImportService: CSV import with validation and progress
- Typed models: Portfolio.php, Testimonial.php, Settings.php
- Content filters: the_content, excerpt_length, body_class
- Auth filters: login_redirect, authenticate
- Custom rewrite rules
- PHPUnit integration tests: REST CRUD, meta box, cron, shortcode output
- PHPCS WordPress-Extra standard — zero errors
- PHPStan level 8 with WordPress stubs — zero errors
- Brain\Monkey for unit-testable hook/filter code
- GitHub Actions CI: PHPCS → PHPStan → PHPUnit → block JS tests → build
- docs/: ARCHITECTURE.md, TESTING.md, HOOKS-REFERENCE.md, REST-API.md, CLI-COMMANDS.md, DEPLOYMENT.md, SECURITY.md, CHANGELOG.md

### Requires
- WordPress 6.8+, PHP 8.3+
- wp-starter-theme (companion theme) for frontend templates
