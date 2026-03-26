# Testing

## Prerequisites

- PHP 8.5+
- Composer
- WP-CLI (optional, for integration smoke tests)

## Install Dependencies

```bash
composer install
```

## Run Tests

```bash
# PHPUnit (all suites)
composer test

# Static analysis
composer phpstan

# Coding standards
composer phpcs
```

## Test Pyramid

```
         /\
        /  \   Integration (tests/Integration/)
       /----\
      /      \  Unit (tests/Unit/)
     /--------\
```

### Unit Tests

Located in `tests/Unit/`. Use [Brain Monkey](https://brain-wp.github.io/BrainMonkey/) to stub WordPress functions and hooks without a live WordPress installation.

Key test files:

| File | Covers |
|---|---|
| `tests/Unit/ContainerTest.php` | DI container bind, singleton, auto-wire, NotFoundException |
| `tests/Unit/Models/SettingsTest.php` | Settings model defaults and get_option fallback |

### Integration Tests

Located in `tests/Integration/`. Require a bootstrapped WordPress environment (e.g., via WP-CLI's `--require` or a full test database). Not run in CI by default — use:

```bash
vendor/bin/phpunit --testsuite Integration
```

## Brain Monkey Setup

`tests/bootstrap.php` calls `Brain\Monkey\setUp()` globally. Each test class must call `Monkey\setUp()` in `setUp()` and `Monkey\tearDown()` in `tearDown()` to isolate mock state between tests.

```php
protected function setUp(): void
{
    parent::setUp();
    Monkey\setUp();
}

protected function tearDown(): void
{
    Monkey\tearDown();
    parent::tearDown();
}
```

## PHPStan

Level 8 analysis configured in `phpstan.neon`. WordPress stubs are loaded from `php-stubs/wordpress-stubs`.

```bash
composer phpstan
```

## PHPCS

WordPress Extra coding standard enforced via `phpcs.xml`.

```bash
composer phpcs
```

Fix auto-fixable violations:

```bash
vendor/bin/phpcbf --standard=WordPress-Extra src/
```
