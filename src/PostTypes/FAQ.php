<?php
/**
 * FAQ custom post type registration.
 *
 * @package WPStarterPlugin\PostTypes
 */

declare(strict_types=1);

namespace WPStarterPlugin\PostTypes;

use WPStarterPlugin\Traits\HasMeta;

/**
 * Registers the `faq` CPT with meta for ordering and category grouping.
 */
class FAQ {

	use HasMeta;

	public const POST_TYPE   = 'faq';
	public const META_PREFIX = '_faq_';

	/**
	 * Registers the custom post type with WordPress.
	 *
	 * @return void
	 */
	public function register(): void {
		register_post_type(
			self::POST_TYPE,
			[
				'labels'          => $this->getLabels(),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => true,
				'show_in_rest'    => true,
				'rest_base'       => 'faqs',
				'supports'        => [ 'title', 'editor' ],
				'menu_icon'       => 'dashicons-editor-help',
				'capability_type' => 'post',
				'map_meta_cap'    => true,
				'menu_position'   => 27,
			]
		);

		register_post_meta(
			self::POST_TYPE,
			self::META_PREFIX . 'order',
			[
				'type'          => 'integer',
				'description'   => __( 'Display order', 'wp-starter-plugin' ),
				'single'        => true,
				'show_in_rest'  => true,
				'auth_callback' => fn() => current_user_can( 'edit_posts' ),
			]
		);
	}

	/**
	 * Returns the CPT labels array.
	 *
	 * @return array<string, string>
	 */
	private function getLabels(): array {
		return [
			'name'          => __( 'FAQs', 'wp-starter-plugin' ),
			'singular_name' => __( 'FAQ', 'wp-starter-plugin' ),
			'add_new'       => __( 'Add New', 'wp-starter-plugin' ),
			'add_new_item'  => __( 'Add New FAQ', 'wp-starter-plugin' ),
			'edit_item'     => __( 'Edit FAQ', 'wp-starter-plugin' ),
			'all_items'     => __( 'All FAQs', 'wp-starter-plugin' ),
			'not_found'     => __( 'No FAQs found.', 'wp-starter-plugin' ),
			'menu_name'     => __( 'FAQs', 'wp-starter-plugin' ),
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
	 * Returns the display order for a FAQ item.
	 *
	 * @param int $postId Post ID.
	 * @return int
	 */
	public function getOrder( int $postId ): int {
		$value = $this->getMeta( $postId, 'order' );
		return is_numeric( $value ) ? (int) $value : 0;
	}
}
