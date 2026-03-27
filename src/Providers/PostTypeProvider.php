<?php
/**
 * Service provider for custom post type registration.
 *
 * @package WPStarterPlugin\Providers
 */

declare(strict_types=1);

namespace WPStarterPlugin\Providers;

use WPStarterPlugin\PostTypes\FAQ;
use WPStarterPlugin\PostTypes\Portfolio;
use WPStarterPlugin\PostTypes\Testimonial;

/**
 * Registers the Portfolio, Testimonial, and FAQ custom post types on `init`.
 */
class PostTypeProvider {

	/**
	 * Registers WordPress hooks for CPT registration.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'init', array( $this, 'registerPostTypes' ) );
	}

	/**
	 * Instantiates and registers all CPTs.
	 *
	 * @return void
	 */
	public function registerPostTypes(): void {
		( new Portfolio() )->register();
		( new Testimonial() )->register();
		( new FAQ() )->register();
	}
}
