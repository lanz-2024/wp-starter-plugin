<?php
/**
 * PHPUnit bootstrap for wp-starter-plugin tests.
 *
 * Uses Brain\Monkey for unit-testable WordPress function mocking.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Brain\Monkey;

// Bootstrap Brain\Monkey globally.
\PHPUnit\Framework\TestCase::class;
