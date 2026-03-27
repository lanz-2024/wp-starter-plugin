<?php
/**
 * WP-CLI seed command.
 *
 * @package WPStarterPlugin\CLI
 */

declare(strict_types=1);

namespace WPStarterPlugin\CLI;

use WP_CLI;
use WP_CLI_Command;

/**
 * WP-CLI command for seeding test data.
 *
 * ## EXAMPLES
 *
 *     wp starter seed --count=20
 */
class SeedCommand extends WP_CLI_Command {

	/**
	 * Seed portfolio items.
	 *
	 * ## OPTIONS
	 *
	 * [--count=<count>]
	 * : Number of items to create. Default: 10.
	 *
	 * ## EXAMPLES
	 *
	 *     wp starter seed --count=20
	 *
	 * @param array<int,string>    $args       Positional arguments.
	 * @param array<string,string> $assoc_args Associative arguments.
	 * @return void
	 */
	public function __invoke( array $args, array $assoc_args ): void {
		$count = (int) ( $assoc_args['count'] ?? 10 );

		$progress = \WP_CLI\Utils\make_progress_bar( "Creating {$count} portfolio items", $count );

		for ( $i = 1; $i <= $count; $i++ ) {
			wp_insert_post(
				array(
					'post_type'    => 'portfolio',
					'post_title'   => "Portfolio Item {$i}",
					'post_status'  => 'publish',
					'post_content' => "Sample content for portfolio item {$i}.",
				)
			);

			$progress->tick();
		}

		$progress->finish();
		WP_CLI::success( "Created {$count} portfolio items." );
	}
}
