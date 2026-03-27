<?php
/**
 * Portfolio custom post type registration and meta management.
 *
 * @package WPStarterPlugin\PostTypes
 */

declare(strict_types=1);

namespace WPStarterPlugin\PostTypes;

use WPStarterPlugin\Traits\HasMeta;

/**
 * Registers the `portfolio` CPT and exposes typed meta accessors.
 */
class Portfolio {

	use HasMeta;

	public const POST_TYPE   = 'portfolio';
	public const META_PREFIX = '_portfolio_';

	/**
	 * Registers the custom post type with WordPress.
	 *
	 * @return void
	 */
	public function register(): void {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'                => $this->getLabels(),
				'public'                => true,
				'has_archive'           => true,
				'rewrite'               => array(
					'slug'       => 'portfolio',
					'with_front' => false,
				),
				'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
				'show_in_rest'          => true,
				'rest_base'             => 'portfolio',
				'rest_controller_class' => \WP_REST_Posts_Controller::class,
				'menu_icon'             => 'dashicons-portfolio',
				'capability_type'       => 'post',
				'map_meta_cap'          => true,
				'show_in_menu'          => true,
				'menu_position'         => 25,
			)
		);

		$this->registerMetaFields();
	}

	/**
	 * Registers all portfolio meta fields for the REST API and block editor.
	 *
	 * @return void
	 */
	private function registerMetaFields(): void {
		$fields = array(
			'url'          => array(
				'type'        => 'string',
				'description' => __( 'Project URL', 'wp-starter-plugin' ),
			),
			'repo_url'     => array(
				'type'        => 'string',
				'description' => __( 'Repository URL', 'wp-starter-plugin' ),
			),
			'client'       => array(
				'type'        => 'string',
				'description' => __( 'Client name', 'wp-starter-plugin' ),
			),
			'year'         => array(
				'type'        => 'integer',
				'description' => __( 'Project year', 'wp-starter-plugin' ),
			),
			'featured'     => array(
				'type'        => 'boolean',
				'description' => __( 'Featured item', 'wp-starter-plugin' ),
			),
			'technologies' => array(
				'type'        => 'string',
				'description' => __( 'Comma-separated technologies', 'wp-starter-plugin' ),
			),
		);

		foreach ( $fields as $key => $schema ) {
			register_post_meta(
				self::POST_TYPE,
				self::META_PREFIX . $key,
				array(
					'type'              => $schema['type'],
					'description'       => $schema['description'],
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => 'sanitize_text_field',
					'auth_callback'     => fn() => current_user_can( 'edit_posts' ),
				)
			);
		}
	}

	/**
	 * Returns the CPT labels array.
	 *
	 * @return array<string, string>
	 */
	private function getLabels(): array {
		return array(
			'name'               => __( 'Portfolio', 'wp-starter-plugin' ),
			'singular_name'      => __( 'Portfolio Item', 'wp-starter-plugin' ),
			'add_new'            => __( 'Add New Item', 'wp-starter-plugin' ),
			'add_new_item'       => __( 'Add New Portfolio Item', 'wp-starter-plugin' ),
			'edit_item'          => __( 'Edit Portfolio Item', 'wp-starter-plugin' ),
			'view_item'          => __( 'View Portfolio Item', 'wp-starter-plugin' ),
			'all_items'          => __( 'All Portfolio Items', 'wp-starter-plugin' ),
			'search_items'       => __( 'Search Portfolio', 'wp-starter-plugin' ),
			'not_found'          => __( 'No portfolio items found.', 'wp-starter-plugin' ),
			'not_found_in_trash' => __( 'No portfolio items found in Trash.', 'wp-starter-plugin' ),
			'menu_name'          => __( 'Portfolio', 'wp-starter-plugin' ),
		);
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
	 * Returns the project URL for a portfolio item.
	 *
	 * @param int $postId Post ID.
	 * @return string
	 */
	public function getProjectUrl( int $postId ): string {
		$value = $this->getMeta( $postId, 'url' );
		return is_string( $value ) ? $value : '';
	}

	/**
	 * Returns the repository URL for a portfolio item.
	 *
	 * @param int $postId Post ID.
	 * @return string
	 */
	public function getRepoUrl( int $postId ): string {
		$value = $this->getMeta( $postId, 'repo_url' );
		return is_string( $value ) ? $value : '';
	}

	/**
	 * Returns the client name for a portfolio item.
	 *
	 * @param int $postId Post ID.
	 * @return string
	 */
	public function getClient( int $postId ): string {
		$value = $this->getMeta( $postId, 'client' );
		return is_string( $value ) ? $value : '';
	}

	/**
	 * Returns the project year for a portfolio item.
	 *
	 * @param int $postId Post ID.
	 * @return int
	 */
	public function getYear( int $postId ): int {
		$value = $this->getMeta( $postId, 'year' );
		return is_numeric( $value ) ? (int) $value : (int) gmdate( 'Y' );
	}

	/**
	 * Returns whether the portfolio item is featured.
	 *
	 * @param int $postId Post ID.
	 * @return bool
	 */
	public function isFeatured( int $postId ): bool {
		return (bool) $this->getMeta( $postId, 'featured' );
	}

	/**
	 * Returns the technologies list for a portfolio item.
	 *
	 * @param int $postId Post ID.
	 * @return list<string>
	 */
	public function getTechnologies( int $postId ): array {
		$raw = $this->getMeta( $postId, 'technologies' );
		if ( ! is_string( $raw ) || $raw === '' ) {
			return array();
		}
		return array_values( array_filter( array_map( 'trim', explode( ',', $raw ) ) ) );
	}
}
