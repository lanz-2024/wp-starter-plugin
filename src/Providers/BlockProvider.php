<?php
/**
 * Service provider for Gutenberg block registration.
 *
 * @package WPStarterPlugin\Providers
 */

declare(strict_types=1);

namespace WPStarterPlugin\Providers;

/**
 * Registers plugin Gutenberg blocks from the /blocks/ directory on `init`.
 */
class BlockProvider {

	/**
	 * Registers the `init` hook for block registration.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'init', array( $this, 'registerBlocks' ) );
	}

	/**
	 * Iterates block directories and calls register_block_type for each.
	 *
	 * Each block must have a block.json file in its subdirectory under /blocks/.
	 *
	 * @return void
	 */
	public function registerBlocks(): void {
		$blocks_dir = WP_STARTER_PLUGIN_DIR . 'blocks/';

		if ( ! is_dir( $blocks_dir ) ) {
			return;
		}

		$entries = scandir( $blocks_dir );
		if ( $entries === false ) {
			return;
		}

		foreach ( $entries as $entry ) {
			if ( in_array( $entry, array( '.', '..' ), true ) ) {
				continue;
			}

			$block_path = $blocks_dir . $entry;
			if ( is_dir( $block_path ) && file_exists( $block_path . '/block.json' ) ) {
				register_block_type( $block_path );
			}
		}
	}
}
