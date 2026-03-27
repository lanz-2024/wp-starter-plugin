<?php
/**
 * Service provider for custom taxonomy registration.
 *
 * @package WPStarterPlugin\Providers
 */

declare(strict_types=1);

namespace WPStarterPlugin\Providers;

use WPStarterPlugin\PostTypes\Portfolio;
use WPStarterPlugin\PostTypes\Testimonial;

/**
 * Registers the Skill and Industry custom taxonomies on `init`.
 */
class TaxonomyProvider {

	/**
	 * Registers WordPress hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'init', array( $this, 'registerTaxonomies' ) );
	}

	/**
	 * Registers all plugin taxonomies.
	 *
	 * @return void
	 */
	public function registerTaxonomies(): void {
		$this->registerSkill();
		$this->registerIndustry();
	}

	/**
	 * Registers the Skill taxonomy attached to Portfolio items.
	 *
	 * @return void
	 */
	private function registerSkill(): void {
		register_taxonomy(
			'skill',
			array( Portfolio::POST_TYPE ),
			array(
				'labels'            => array(
					'name'          => __( 'Skills', 'wp-starter-plugin' ),
					'singular_name' => __( 'Skill', 'wp-starter-plugin' ),
					'search_items'  => __( 'Search Skills', 'wp-starter-plugin' ),
					'all_items'     => __( 'All Skills', 'wp-starter-plugin' ),
					'edit_item'     => __( 'Edit Skill', 'wp-starter-plugin' ),
					'add_new_item'  => __( 'Add New Skill', 'wp-starter-plugin' ),
					'not_found'     => __( 'No skills found.', 'wp-starter-plugin' ),
					'menu_name'     => __( 'Skills', 'wp-starter-plugin' ),
				),
				'hierarchical'      => false,
				'public'            => true,
				'show_in_rest'      => true,
				'rest_base'         => 'skills',
				'rewrite'           => array(
					'slug'       => 'skill',
					'with_front' => false,
				),
				'show_admin_column' => true,
			)
		);
	}

	/**
	 * Registers the Industry taxonomy attached to Portfolio and Testimonial items.
	 *
	 * @return void
	 */
	private function registerIndustry(): void {
		register_taxonomy(
			'industry',
			array( Portfolio::POST_TYPE, Testimonial::POST_TYPE ),
			array(
				'labels'            => array(
					'name'          => __( 'Industries', 'wp-starter-plugin' ),
					'singular_name' => __( 'Industry', 'wp-starter-plugin' ),
					'search_items'  => __( 'Search Industries', 'wp-starter-plugin' ),
					'all_items'     => __( 'All Industries', 'wp-starter-plugin' ),
					'edit_item'     => __( 'Edit Industry', 'wp-starter-plugin' ),
					'add_new_item'  => __( 'Add New Industry', 'wp-starter-plugin' ),
					'not_found'     => __( 'No industries found.', 'wp-starter-plugin' ),
					'menu_name'     => __( 'Industries', 'wp-starter-plugin' ),
				),
				'hierarchical'      => true,
				'public'            => true,
				'show_in_rest'      => true,
				'rest_base'         => 'industries',
				'rewrite'           => array(
					'slug'       => 'industry',
					'with_front' => false,
				),
				'show_admin_column' => true,
			)
		);
	}
}
