<?php
/**
 * Uninstall routine for WP Starter Plugin.
 *
 * Removes all plugin data: options, custom tables, scheduled events,
 * and post meta when the plugin is deleted via the WordPress admin.
 *
 * @package WPStarterPlugin
 */

declare(strict_types=1);

// Only run when WordPress triggers an uninstall.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Remove plugin options.
$options = [
	'wp_starter_plugin_version',
	'wp_starter_settings',
	'wp_starter_cache_version',
];

foreach ( $options as $option ) {
	delete_option( $option );
}

// Remove scheduled cron events.
$timestamps = [
	wp_next_scheduled( 'wp_starter_cleanup' ),
	wp_next_scheduled( 'wp_starter_sync' ),
];

foreach ( $timestamps as $timestamp ) {
	if ( $timestamp ) {
		wp_unschedule_event( $timestamp, 'wp_starter_cleanup' );
	}
}

wp_clear_scheduled_hook( 'wp_starter_cleanup' );
wp_clear_scheduled_hook( 'wp_starter_sync' );

// Remove all transients.
$wpdb->query(
	"DELETE FROM {$wpdb->options}
	 WHERE option_name LIKE '_transient_wp_starter_%'
	 OR option_name LIKE '_transient_timeout_wp_starter_%'"
);

// Remove all portfolio post meta.
$wpdb->query(
	"DELETE FROM {$wpdb->postmeta}
	 WHERE meta_key LIKE '_portfolio_%'"
);

// Optionally remove CPT posts (uncomment to enable hard delete on uninstall).
// $post_types = [ 'portfolio', 'testimonial', 'faq' ];
// foreach ( $post_types as $post_type ) {
// 	$posts = get_posts( [ 'post_type' => $post_type, 'numberposts' => -1, 'post_status' => 'any' ] );
// 	foreach ( $posts as $post ) {
// 		wp_delete_post( $post->ID, true );
// 	}
// }
