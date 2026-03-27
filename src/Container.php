<?php
/**
 * PSR-11 compatible DI Container with auto-wiring.
 *
 * @package WPStarterPlugin
 */

declare(strict_types=1);

namespace WPStarterPlugin;

use Closure;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use RuntimeException;

/**
 * Minimal PSR-11 DI container supporting lazy singletons, factory bindings,
 * and reflection-based auto-wiring for zero-arg or type-hinted constructors.
 */
class Container implements ContainerInterface {

	/**
	 * Registered factory closures keyed by abstract identifier.
	 *
	 * @var array<string, Closure(Container): object>
	 */
	private array $bindings = array();

	/**
	 * Resolved singleton instances keyed by abstract identifier.
	 *
	 * @var array<string, object>
	 */
	private array $singletons = array();

	/**
	 * Registers a transient factory binding.
	 *
	 * @param string                    $abstract The identifier / class name.
	 * @param Closure(Container): object $factory  Factory producing a new instance each call.
	 * @return void
	 */
	public function bind( string $abstract, Closure $factory ): void {
		$this->bindings[ $abstract ] = $factory;
	}

	/**
	 * Registers a singleton binding — the factory is called once and the result
	 * is cached for all subsequent resolutions.
	 *
	 * @param string                    $abstract The identifier / class name.
	 * @param Closure(Container): object $factory  Factory producing the singleton.
	 * @return void
	 */
	public function singleton( string $abstract, Closure $factory ): void {
		$this->bindings[ $abstract ] = function () use ( $abstract, $factory ): object {
			if ( ! isset( $this->singletons[ $abstract ] ) ) {
				$this->singletons[ $abstract ] = $factory( $this );
			}
			return $this->singletons[ $abstract ];
		};
	}

	/**
	 * Resolves an identifier from the container.
	 *
	 * Falls back to reflection-based auto-wiring when no binding is registered.
	 *
	 * @param string $abstract The identifier / fully-qualified class name.
	 * @return object
	 * @throws NotFoundException When the identifier cannot be resolved.
	 */
	public function make( string $abstract ): object {
		if ( isset( $this->bindings[ $abstract ] ) ) {
			return ( $this->bindings[ $abstract ] )( $this );
		}

		if ( class_exists( $abstract ) ) {
			return $this->autoWire( $abstract );
		}

		throw new NotFoundException( "Cannot resolve [{$abstract}] from the container." );
	}

	/**
	 * PSR-11: Returns an entry by its identifier.
	 *
	 * @param string $id Identifier of the entry.
	 * @return object
	 * @throws NotFoundException When the entry is not found.
	 */
	public function get( string $id ): object {
		return $this->make( $id );
	}

	/**
	 * PSR-11: Returns true if the container can return an entry for the given identifier.
	 *
	 * @param string $id Identifier of the entry.
	 * @return bool
	 */
	public function has( string $id ): bool {
		return isset( $this->bindings[ $id ] ) || class_exists( $id );
	}

	/**
	 * Instantiates a class by resolving its constructor parameters via the container.
	 *
	 * @param string $class Fully-qualified class name.
	 * @return object
	 * @throws NotFoundException When a dependency cannot be auto-wired.
	 */
	private function autoWire( string $class ): object {
		$ref         = new ReflectionClass( $class );
		$constructor = $ref->getConstructor();

		if ( $constructor === null || $constructor->getNumberOfParameters() === 0 ) {
			return $ref->newInstance();
		}

		$params = array_map(
			function ( ReflectionParameter $param ) use ( $class ): object {
				$type = $param->getType();

				if ( ! $type instanceof ReflectionNamedType || $type->isBuiltin() ) {
					throw new NotFoundException(
						"Cannot auto-wire parameter '\${$param->getName()}' in {$class}: no type hint or built-in type."
					);
				}

				return $this->make( $type->getName() );
			},
			$constructor->getParameters()
		);

		return $ref->newInstanceArgs( $params );
	}
}
