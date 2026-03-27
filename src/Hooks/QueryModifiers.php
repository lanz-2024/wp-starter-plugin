<?php
/**
 * Query modification hooks.
 *
 * @package WPStarterPlugin\Hooks
 */

declare(strict_types=1);

namespace WPStarterPlugin\Hooks;

use WP_Query;

/**
 * Query modification hooks.
 */
class QueryModifiers {

	/**
	 * Register query modifier hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'pre_get_posts', array( $this, 'modify_portfolio_archive' ) );
	}

	/**
	 * Customize the portfolio archive query.
	 *
	 * @param WP_Query $query The WP_Query instance (passed by reference).
	 * @return void
	 */
	public function modify_portfolio_archive( WP_Query $query ): void {
		if (
			! is_admin()
			&& $query->is_main_query()
			&& $query->is_post_type_archive( 'portfolio' )
		) {
			$query->set( 'posts_per_page', 12 );
			$query->set( 'orderby', 'date' );
			$query->set( 'order', 'DESC' );
		}
	}
}
