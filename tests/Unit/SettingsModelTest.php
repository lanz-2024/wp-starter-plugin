<?php

declare(strict_types=1);

namespace WPStarterPlugin\Tests\Unit;

use Brain\Monkey;
use Brain\Monkey\Functions;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use WPStarterPlugin\Models\Settings;

/**
 * Tests for the Settings model (Options API wrapper).
 */
class SettingsModelTest extends TestCase
{
    use MockeryPHPUnitIntegration;

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

        $this->assertSame(10, $settings->get('per_page', 10));
    }

    public function test_get_returns_stored_value(): void
    {
        Functions\when('get_option')->justReturn(['per_page' => 25]);

        $settings = new Settings();

        $this->assertSame(25, $settings->get('per_page', 10));
    }

    public function test_update_calls_update_option(): void
    {
        Functions\when('get_option')->justReturn([]);
        Functions\expect('update_option')
            ->once()
            ->with('wp_starter_plugin_settings', \Mockery::type('array'));

        $settings = new Settings();
        $settings->set('per_page', 50);

        // Assertion is in the expectation above.
        $this->addToAssertionCount(1);
    }
}
