<?php
/**
 * HasHooks trait — centralised hook registration helpers.
 *
 * @package WPStarterPlugin\Traits
 */

declare(strict_types=1);

namespace WPStarterPlugin\Traits;

/**
 * Provides addAction() / addFilter() wrappers that default the callback to $this,
 * making hook registration inside service classes more ergonomic.
 */
trait HasHooks {

	/**
	 * Adds an action hook using a method on this class as the callback.
	 *
	 * @param string $hook     WordPress action tag.
	 * @param string $method   Method name on $this.
	 * @param int    $priority Optional. Hook priority. Default 10.
	 * @param int    $args     Optional. Accepted argument count. Default 1.
	 * @return void
	 */
	protected function addAction( string $hook, string $method, int $priority = 10, int $args = 1 ): void {
		add_action( $hook, [ $this, $method ], $priority, $args );
	}

	/**
	 * Adds a filter hook using a method on this class as the callback.
	 *
	 * @param string $hook     WordPress filter tag.
	 * @param string $method   Method name on $this.
	 * @param int    $priority Optional. Hook priority. Default 10.
	 * @param int    $args     Optional. Accepted argument count. Default 1.
	 * @return void
	 */
	protected function addFilter( string $hook, string $method, int $priority = 10, int $args = 1 ): void {
		add_filter( $hook, [ $this, $method ], $priority, $args );
	}

	/**
	 * Removes a previously registered action hook for a method on this class.
	 *
	 * @param string $hook     WordPress action tag.
	 * @param string $method   Method name on $this.
	 * @param int    $priority Optional. Hook priority. Default 10.
	 * @return void
	 */
	protected function removeAction( string $hook, string $method, int $priority = 10 ): void {
		remove_action( $hook, [ $this, $method ], $priority );
	}

	/**
	 * Removes a previously registered filter hook for a method on this class.
	 *
	 * @param string $hook     WordPress filter tag.
	 * @param string $method   Method name on $this.
	 * @param int    $priority Optional. Hook priority. Default 10.
	 * @return void
	 */
	protected function removeFilter( string $hook, string $method, int $priority = 10 ): void {
		remove_filter( $hook, [ $this, $method ], $priority );
	}
}
