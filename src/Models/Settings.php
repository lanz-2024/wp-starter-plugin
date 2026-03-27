<?php
/**
 * Plugin settings model.
 *
 * @package WPStarterPlugin\Models
 */

declare(strict_types=1);

namespace WPStarterPlugin\Models;

/**
 * Plugin settings wrapper with typed accessors.
 */
class Settings {

	/**
	 * WordPress option key for plugin settings.
	 */
	private const OPTION_KEY = 'wp_starter_plugin_settings';

	/**
	 * Default setting values.
	 *
	 * @var array<string,mixed>
	 */
	private array $defaults = array(
		'items_per_page' => 10,
		'enable_cache'   => true,
		'cache_ttl'      => 3600,
	);

	/**
	 * Merged settings data (saved + defaults).
	 *
	 * @var array<string,mixed>
	 */
	private array $data;

	/**
	 * Constructs the Settings model, merging saved values with defaults.
	 */
	public function __construct() {
		$saved      = get_option( self::OPTION_KEY, array() );
		$this->data = is_array( $saved ) ? array_merge( $this->defaults, $saved ) : $this->defaults;
	}

	/**
	 * Get a setting value.
	 *
	 * @param string $key     Setting key.
	 * @param mixed  $default Fallback value.
	 * @return mixed
	 */
	public function get( string $key, mixed $default = null ): mixed {
		return $this->data[ $key ] ?? $default ?? $this->defaults[ $key ] ?? null;
	}

	/**
	 * Update a setting value and persist to the database.
	 *
	 * @param string $key   Setting key.
	 * @param mixed  $value New value.
	 * @return void
	 */
	public function set( string $key, mixed $value ): void {
		$this->data[ $key ] = $value;
		update_option( self::OPTION_KEY, $this->data );
	}
}
