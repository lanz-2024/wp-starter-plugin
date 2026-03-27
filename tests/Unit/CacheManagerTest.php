<?php

declare(strict_types=1);

namespace WPStarterPlugin\Tests\Unit;

use Brain\Monkey;
use Brain\Monkey\Functions;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use WPStarterPlugin\Services\CacheManager;

/**
 * Tests for the CacheManager (transient + object cache strategy).
 */
class CacheManagerTest extends TestCase
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

    public function test_remember_returns_cached_value_on_hit(): void
    {
        Functions\when('get_transient')->justReturn('cached_value');

        $cache  = new CacheManager();
        $result = $cache->remember('my_key', 3600, static fn() => 'fresh_value');

        $this->assertSame('cached_value', $result);
    }

    public function test_remember_calls_callback_on_miss(): void
    {
        Functions\when('get_transient')->justReturn(false);
        Functions\when('set_transient')->justReturn(true);

        $callbackCalled = false;
        $cache          = new CacheManager();

        $result = $cache->remember('my_key', 3600, static function () use (&$callbackCalled) {
            $callbackCalled = true;
            return 'fresh_value';
        });

        $this->assertTrue($callbackCalled);
        $this->assertSame('fresh_value', $result);
    }

    public function test_forget_deletes_transient(): void
    {
        Functions\expect('delete_transient')
            ->once()
            ->with('my_key')
            ->andReturn(true);

        $cache = new CacheManager();
        $cache->forget('my_key');

        $this->addToAssertionCount(1);
    }
}
