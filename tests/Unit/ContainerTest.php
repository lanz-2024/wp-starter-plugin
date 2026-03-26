<?php
declare(strict_types=1);

namespace WPStarterPlugin\Tests\Unit;

use Brain\Monkey;
use PHPUnit\Framework\TestCase;
use WPStarterPlugin\Container;

class ContainerTest extends TestCase
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

    public function test_bind_and_make(): void
    {
        $container = new Container();
        $container->bind('greeting', fn() => 'Hello, World!');
        $this->assertSame('Hello, World!', $container->make('greeting'));
    }

    public function test_singleton_returns_same_instance(): void
    {
        $container = new Container();
        $container->singleton('obj', fn() => new \stdClass());
        $a = $container->make('obj');
        $b = $container->make('obj');
        $this->assertSame($a, $b);
    }

    public function test_make_throws_on_missing_binding(): void
    {
        $this->expectException(\RuntimeException::class);
        $container = new Container();
        $container->make('nonexistent');
    }
}
