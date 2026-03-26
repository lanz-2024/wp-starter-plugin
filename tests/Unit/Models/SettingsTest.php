<?php
declare(strict_types=1);

namespace WPStarterPlugin\Tests\Unit\Models;

use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;
use WPStarterPlugin\Models\Settings;

class SettingsTest extends TestCase
{
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

    public function test_get_returns_default_when_option_missing(): void
    {
        Functions\when('get_option')->justReturn(false);
        $settings = new Settings();
        $this->assertSame(10, $settings->get('items_per_page'));
    }
}
