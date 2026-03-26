<?php
/**
 * Service provider for WP Cron schedule and event registration.
 *
 * @package WPStarterPlugin\Providers
 */

declare(strict_types=1);

namespace WPStarterPlugin\Providers;

/**
 * Registers custom cron schedules and schedules plugin recurring events.
 */
class CronProvider {

	/**
	 * Registers cron schedule filter and schedules the plugin's recurring events.
	 *
	 * @return void
	 */
	public function register(): void {
		add_filter( 'cron_schedules', [ $this, 'addSchedules' ] );
		add_action( 'wsp_cleanup_transients', [ $this, 'cleanupTransients' ] );
		add_action( 'wsp_sync_data', [ $this, 'syncData' ] );

		if ( ! wp_next_scheduled( 'wsp_cleanup_transients' ) ) {
			wp_schedule_event( time(), 'daily', 'wsp_cleanup_transients' );
		}

		if ( ! wp_next_scheduled( 'wsp_sync_data' ) ) {
			wp_schedule_event( time(), 'hourly', 'wsp_sync_data' );
		}
	}

	/**
	 * Appends custom cron intervals to the WordPress schedule list.
	 *
	 * @param array<string, array<string, int|string>> $schedules Existing schedules.
	 * @return array<string, array<string, int|string>>
	 */
	public function addSchedules( array $schedules ): array {
		if ( ! isset( $schedules['every_six_hours'] ) ) {
			$schedules['every_six_hours'] = [
				'interval' => 6 * HOUR_IN_SECONDS,
				'display'  => __( 'Every 6 Hours', 'wp-starter-plugin' ),
			];
		}

		return $schedules;
	}

	/**
	 * Removes expired plugin transients from the options table.
	 *
	 * @return void
	 */
	public function cleanupTransients(): void {
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

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'WP Starter Plugin: transient cleanup ran at ' . gmdate( 'Y-m-d H:i:s' ) );
		}
	}

	/**
	 * Stub for the hourly sync job — extendable via action hook.
	 *
	 * @return void
	 */
	public function syncData(): void {
		/**
		 * Fires during the hourly wsp_sync_data cron event.
		 *
		 * @since 0.1.0
		 */
		do_action( 'wsp_sync_data' );
	}
}
