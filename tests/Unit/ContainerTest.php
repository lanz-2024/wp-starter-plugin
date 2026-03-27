<?php

declare(strict_types=1);

namespace WPStarterPlugin\Tests\Unit;

use Brain\Monkey;
use Brain\Monkey\Functions;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use WPStarterPlugin\Container;

/**
 * Tests for the PSR-11 DI container.
 */
class ContainerTest extends TestCase
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

    public function test_bind_and_make_returns_instance(): void
    {
        $container = new Container();
        $container->bind('foo', static fn() => new \stdClass());

        $instance = $container->make('foo');

        $this->assertInstanceOf(\stdClass::class, $instance);
    }

    public function test_singleton_returns_same_instance(): void
    {
        $container = new Container();
        $container->singleton('bar', static fn() => new \stdClass());

        $first  = $container->make('bar');
        $second = $container->make('bar');

        $this->assertSame($first, $second);
    }

    public function test_has_returns_true_for_bound_id(): void
    {
        $container = new Container();
        $container->bind('baz', static fn() => null);

        $this->assertTrue($container->has('baz'));
    }

    public function test_has_returns_false_for_unbound_id(): void
    {
        $container = new Container();

        $this->assertFalse($container->has('unbound'));
    }

    public function test_get_throws_for_unknown_id(): void
    {
        $container = new Container();

        $this->expectException(\InvalidArgumentException::class);
        $container->get('does_not_exist');
    }
}
