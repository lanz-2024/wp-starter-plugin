<?php
/**
 * Cache manager — transients + object cache abstraction.
 *
 * @package WPStarterPlugin\Services
 */

declare(strict_types=1);

namespace WPStarterPlugin\Services;

/**
 * Wraps WordPress transient and object cache APIs into a single interface.
 *
 * When a persistent object-cache (e.g. Redis via wp-redis) is active, all
 * operations use `wp_cache_*`. Otherwise they fall back to transients so the
 * plugin works on vanilla installs with no external cache.
 */
class CacheManager {

	private bool $objectCacheAvailable;

	/**
	 * Detects whether a persistent object cache is active.
	 */
	public function __construct() {
		$this->objectCacheAvailable = wp_using_ext_object_cache();
	}

	/**
	 * Retrieves a cached value.
	 *
	 * @param string $key   Cache key (without prefix for transients).
	 * @param string $group Object-cache group. Ignored when using transients.
	 * @return mixed False on cache miss.
	 */
	public function get( string $key, string $group = 'wp_starter' ): mixed {
		if ( $this->objectCacheAvailable ) {
			return wp_cache_get( $key, $group );
		}
		return get_transient( $this->prefixKey( $key ) );
	}

	/**
	 * Stores a value in the cache.
	 *
	 * @param string $key        Cache key.
	 * @param mixed  $value      Value to cache.
	 * @param int    $expiration TTL in seconds. Default: HOUR_IN_SECONDS.
	 * @param string $group      Object-cache group.
	 * @return bool
	 */
	public function set( string $key, mixed $value, int $expiration = HOUR_IN_SECONDS, string $group = 'wp_starter' ): bool {
		if ( $this->objectCacheAvailable ) {
			return wp_cache_set( $key, $value, $group, $expiration );
		}
		return set_transient( $this->prefixKey( $key ), $value, $expiration );
	}

	/**
	 * Deletes a cached value.
	 *
	 * @param string $key   Cache key.
	 * @param string $group Object-cache group.
	 * @return bool
	 */
	public function delete( string $key, string $group = 'wp_starter' ): bool {
		if ( $this->objectCacheAvailable ) {
			return wp_cache_delete( $key, $group );
		}
		return delete_transient( $this->prefixKey( $key ) );
	}

	/**
	 * Flushes all entries in an object-cache group, or all plugin transients
	 * when falling back to transients.
	 *
	 * @param string $group Object-cache group. Default: 'wp_starter'.
	 * @return void
	 */
	public function flush( string $group = 'wp_starter' ): void {
		if ( $this->objectCacheAvailable ) {
			wp_cache_flush_group( $group );
			return;
		}

		global $wpdb;
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options}
				 WHERE option_name LIKE %s
				 OR option_name LIKE %s",
				'_transient_wp_starter_%',
				'_transient_timeout_wp_starter_%'
			)
		);
	}

	/**
	 * Returns cached value if present; otherwise calls $callback, caches the
	 * result, and returns it.
	 *
	 * @template T
	 * @param string   $key        Cache key.
	 * @param callable(): T $callback  Produces the value on cache miss.
	 * @param int      $expiration TTL in seconds.
	 * @param string   $group      Object-cache group.
	 * @return T
	 */
	public function remember( string $key, callable $callback, int $expiration = HOUR_IN_SECONDS, string $group = 'wp_starter' ): mixed {
		$cached = $this->get( $key, $group );
		if ( $cached !== false ) {
			return $cached;
		}

		$value = $callback();
		$this->set( $key, $value, $expiration, $group );
		return $value;
	}

	/**
	 * Prefixes a key for use as a transient name.
	 *
	 * @param string $key Raw cache key.
	 * @return string
	 */
	private function prefixKey( string $key ): string {
		return 'wp_starter_' . $key;
	}
}
