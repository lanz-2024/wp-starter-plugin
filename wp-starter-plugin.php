<?php
/**
 * Plugin Name:       WP Starter Plugin
 * Plugin URI:        https://github.com/lanz-2024/wp-starter-plugin
 * Description:       Portfolio plugin demonstrating all major WordPress APIs: CPTs, REST, Gutenberg, WP-CLI, AJAX, Settings, Cron, and more.
 * Version:           0.1.0
 * Requires at least: 6.5
 * Requires PHP:      8.5
 * Author:            Alan Regaya
 * Author URI:        https://github.com/lanz-2024
 * License:           MIT
 * Text Domain:       wp-starter-plugin
 * Domain Path:       /languages
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WP_STARTER_PLUGIN_VERSION', '0.1.0' );
define( 'WP_STARTER_PLUGIN_FILE', __FILE__ );
define( 'WP_STARTER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WP_STARTER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Autoloader.
if ( file_exists( WP_STARTER_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
	require_once WP_STARTER_PLUGIN_DIR . 'vendor/autoload.php';
}

use WPStarterPlugin\Plugin;

/**
 * Returns the singleton Plugin instance.
 *
 * @return Plugin
 */
function wp_starter_plugin(): Plugin {
	static $instance = null;
	if ( $instance === null ) {
		$instance = new Plugin();
	}
	return $instance;
}

register_activation_hook( __FILE__, [ wp_starter_plugin(), 'activate' ] );
register_deactivation_hook( __FILE__, [ wp_starter_plugin(), 'deactivate' ] );

add_action( 'plugins_loaded', [ wp_starter_plugin(), 'boot' ] );
