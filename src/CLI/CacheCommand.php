<?php
/**
 * WP-CLI cache management commands.
 *
 * @package WPStarterPlugin\CLI
 */

declare(strict_types=1);

namespace WPStarterPlugin\CLI;

use WP_CLI;
use WP_CLI_Command;

/**
 * Cache management WP-CLI commands.
 *
 * ## EXAMPLES
 *
 *     wp starter cache flush
 */
class CacheCommand extends WP_CLI_Command {

	/**
	 * Flush plugin transient cache.
	 *
	 * ## EXAMPLES
	 *
	 *     wp starter cache flush
	 *
	 * @param array<int,string>    $args       Positional arguments.
	 * @param array<string,string> $assoc_args Associative arguments.
	 * @return void
	 */
	public function flush( array $args, array $assoc_args ): void {
		global $wpdb;
		$deleted = $wpdb->query(
			"DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_wsp_%'"
		);
		WP_CLI::success( "Flushed {$deleted} plugin transients." );
	}

	/**
	 * Show cache statistics.
	 *
	 * ## EXAMPLES
	 *
	 *     wp starter cache stats
	 *
	 * @param array<int,string>    $args       Positional arguments.
	 * @param array<string,string> $assoc_args Associative arguments.
	 * @return void
	 */
	public function stats( array $args, array $assoc_args ): void {
		global $wpdb;
		$count = (int) $wpdb->get_var(
			"SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE '_transient_wsp_%'"
		);
		WP_CLI::line( "Active plugin transients: {$count}" );
	}
}
