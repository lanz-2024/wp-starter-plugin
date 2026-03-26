<?php
/**
 * Custom rewrite rules.
 *
 * @package WPStarterPlugin\Hooks
 */

declare(strict_types=1);

namespace WPStarterPlugin\Hooks;

/**
 * Custom rewrite rules for the portfolio skill taxonomy.
 */
class RewriteRules {

	/**
	 * Register rewrite-related hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'init', [ $this, 'add_rewrite_rules' ] );
		add_filter( 'query_vars', [ $this, 'add_query_vars' ] );
	}

	/**
	 * Register custom rewrite rules.
	 *
	 * @return void
	 */
	public function add_rewrite_rules(): void {
		add_rewrite_rule(
			'^portfolio/category/([^/]+)/?$',
			'index.php?post_type=portfolio&skill=$matches[1]',
			'top',
		);
	}

	/**
	 * Register custom query vars.
	 *
	 * @param string[] $vars Existing query vars.
	 * @return string[]
	 */
	public function add_query_vars( array $vars ): array {
		$vars[] = 'skill';
		return $vars;
	}
}
