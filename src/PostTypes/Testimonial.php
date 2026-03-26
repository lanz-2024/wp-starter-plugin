<?php
/**
 * Testimonial custom post type registration.
 *
 * @package WPStarterPlugin\PostTypes
 */

declare(strict_types=1);

namespace WPStarterPlugin\PostTypes;

use WPStarterPlugin\Traits\HasMeta;

/**
 * Registers the `testimonial` CPT and exposes typed meta accessors.
 */
class Testimonial {

	use HasMeta;

	public const POST_TYPE   = 'testimonial';
	public const META_PREFIX = '_testimonial_';

	/**
	 * Registers the custom post type with WordPress.
	 *
	 * @return void
	 */
	public function register(): void {
		register_post_type(
			self::POST_TYPE,
			[
				'labels'           => $this->getLabels(),
				'public'           => false,
				'show_ui'          => true,
				'show_in_menu'     => true,
				'show_in_rest'     => true,
				'rest_base'        => 'testimonials',
				'supports'         => [ 'title', 'editor', 'thumbnail' ],
				'menu_icon'        => 'dashicons-format-quote',
				'capability_type'  => 'post',
				'map_meta_cap'     => true,
				'menu_position'    => 26,
			]
		);

		$this->registerMetaFields();
	}

	/**
	 * Registers testimonial-specific meta fields.
	 *
	 * @return void
	 */
	private function registerMetaFields(): void {
		$fields = [
			'author_name'  => [ 'type' => 'string', 'description' => __( 'Author name', 'wp-starter-plugin' ) ],
			'author_title' => [ 'type' => 'string', 'description' => __( 'Author job title', 'wp-starter-plugin' ) ],
			'company'      => [ 'type' => 'string', 'description' => __( 'Company name', 'wp-starter-plugin' ) ],
			'rating'       => [ 'type' => 'integer', 'description' => __( 'Rating (1-5)', 'wp-starter-plugin' ) ],
		];

		foreach ( $fields as $key => $schema ) {
			register_post_meta(
				self::POST_TYPE,
				self::META_PREFIX . $key,
				[
					'type'              => $schema['type'],
					'description'       => $schema['description'],
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => 'sanitize_text_field',
					'auth_callback'     => fn() => current_user_can( 'edit_posts' ),
				]
			);
		}
	}

	/**
	 * Returns the CPT labels array.
	 *
	 * @return array<string, string>
	 */
	private function getLabels(): array {
		return [
			'name'               => __( 'Testimonials', 'wp-starter-plugin' ),
			'singular_name'      => __( 'Testimonial', 'wp-starter-plugin' ),
			'add_new'            => __( 'Add New', 'wp-starter-plugin' ),
			'add_new_item'       => __( 'Add New Testimonial', 'wp-starter-plugin' ),
			'edit_item'          => __( 'Edit Testimonial', 'wp-starter-plugin' ),
			'all_items'          => __( 'All Testimonials', 'wp-starter-plugin' ),
			'not_found'          => __( 'No testimonials found.', 'wp-starter-plugin' ),
			'menu_name'          => __( 'Testimonials', 'wp-starter-plugin' ),
		];
	}

	/**
	 * Returns the meta key prefix for this post type.
	 *
	 * @return string
	 */
	protected function getMetaPrefix(): string {
		return self::META_PREFIX;
	}

	/**
	 * Returns the author name for a testimonial.
	 *
	 * @param int $postId Post ID.
	 * @return string
	 */
	public function getAuthorName( int $postId ): string {
		$value = $this->getMeta( $postId, 'author_name' );
		return is_string( $value ) ? $value : '';
	}

	/**
	 * Returns the author title for a testimonial.
	 *
	 * @param int $postId Post ID.
	 * @return string
	 */
	public function getAuthorTitle( int $postId ): string {
		$value = $this->getMeta( $postId, 'author_title' );
		return is_string( $value ) ? $value : '';
	}

	/**
	 * Returns the company for a testimonial.
	 *
	 * @param int $postId Post ID.
	 * @return string
	 */
	public function getCompany( int $postId ): string {
		$value = $this->getMeta( $postId, 'company' );
		return is_string( $value ) ? $value : '';
	}

	/**
	 * Returns the star rating (1-5) for a testimonial.
	 *
	 * @param int $postId Post ID.
	 * @return int<1, 5>
	 */
	public function getRating( int $postId ): int {
		$value = $this->getMeta( $postId, 'rating' );
		$int   = is_numeric( $value ) ? (int) $value : 5;
		return (int) max( 1, min( 5, $int ) );
	}
}
