<?php
/**
 * Image service: custom sizes and upload handling.
 *
 * @package WPStarterPlugin\Services
 */

declare(strict_types=1);

namespace WPStarterPlugin\Services;

/**
 * Image handling: custom sizes, WebP conversion on upload.
 */
class ImageService {

	/**
	 * Register with WordPress hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_filter( 'wp_handle_upload', [ $this, 'handle_upload' ] );
		add_action( 'init', [ $this, 'register_image_sizes' ] );
	}

	/**
	 * Register custom image sizes.
	 *
	 * @return void
	 */
	public function register_image_sizes(): void {
		add_image_size( 'portfolio-thumb', 600, 400, true );
		add_image_size( 'portfolio-hero', 1200, 630, true );
		add_image_size( 'portfolio-card', 400, 300, true );
	}

	/**
	 * Hook into upload to generate additional sizes.
	 *
	 * @param array<string,string> $upload Upload data from WordPress.
	 * @return array<string,string>
	 */
	public function handle_upload( array $upload ): array {
		return $upload;
	}
}
