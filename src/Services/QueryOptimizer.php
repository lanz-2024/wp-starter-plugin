<?php
/**
 * Query optimiser — builds performant WP_Query argument arrays.
 *
 * @package WPStarterPlugin\Services
 */

declare(strict_types=1);

namespace WPStarterPlugin\Services;

use WPStarterPlugin\PostTypes\Portfolio;

/**
 * Centralises WP_Query argument construction for portfolio queries, ensuring
 * `no_found_rows`, field limiting, and taxonomy filtering are applied
 * consistently across the plugin.
 */
class QueryOptimizer {

	private CacheManager $cache;

	/**
	 * Constructs the optimiser.
	 *
	 * @param CacheManager $cache Cache service for query result caching.
	 */
	public function __construct( CacheManager $cache ) {
		$this->cache = $cache;
	}

	/**
	 * Builds a WP_Query args array for paginated portfolio queries.
	 *
	 * @param string      $postType  Post type slug.
	 * @param int         $perPage   Posts per page.
	 * @param int         $page      Current page number.
	 * @param string      $skill     Optional skill taxonomy slug.
	 * @param string      $industry  Optional industry taxonomy slug.
	 * @param bool|null   $featured  Filter by featured meta when non-null.
	 * @return array<string, mixed>
	 */
	public function buildArgs(
		string $postType,
		int $perPage = 10,
		int $page = 1,
		string $skill = '',
		string $industry = '',
		?bool $featured = null
	): array {
		$args = [
			'post_type'              => $postType,
			'posts_per_page'         => $perPage,
			'paged'                  => $page,
			'post_status'            => 'publish',
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'no_found_rows'          => false, // must be false for pagination totals
			'update_post_term_cache' => true,
			'update_post_meta_cache' => true,
			'ignore_sticky_posts'    => true,
		];

		$tax_query = [];

		if ( $skill !== '' ) {
			$tax_query[] = [
				'taxonomy' => 'skill',
				'field'    => 'slug',
				'terms'    => $skill,
			];
		}

		if ( $industry !== '' ) {
			$tax_query[] = [
				'taxonomy' => 'industry',
				'field'    => 'slug',
				'terms'    => $industry,
			];
		}

		if ( count( $tax_query ) > 1 ) {
			$tax_query['relation'] = 'AND';
		}

		if ( ! empty( $tax_query ) ) {
			$args['tax_query'] = $tax_query;
		}

		if ( $featured !== null ) {
			$args['meta_query'] = [
				[
					'key'     => Portfolio::META_PREFIX . 'featured',
					'value'   => $featured ? '1' : '0',
					'compare' => '=',
				],
			];
		}

		return $args;
	}

	/**
	 * Builds a lean WP_Query args array for a single post lookup.
	 *
	 * Uses `no_found_rows = true` to skip the COUNT(*) query.
	 *
	 * @param string $postType Post type slug.
	 * @param int    $postId   Post ID.
	 * @return array<string, mixed>
	 */
	public function buildSingleArgs( string $postType, int $postId ): array {
		return [
			'post_type'              => $postType,
			'p'                      => $postId,
			'posts_per_page'         => 1,
			'post_status'            => 'publish',
			'no_found_rows'          => true,
			'update_post_term_cache' => true,
			'update_post_meta_cache' => true,
		];
	}

	/**
	 * Returns the cache service for external use if needed.
	 *
	 * @return CacheManager
	 */
	public function getCache(): CacheManager {
		return $this->cache;
	}
}
