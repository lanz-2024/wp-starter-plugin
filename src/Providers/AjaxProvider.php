<?php
/**
 * Service provider for AJAX handler registration.
 *
 * @package WPStarterPlugin\Providers
 */

declare(strict_types=1);

namespace WPStarterPlugin\Providers;

use WPStarterPlugin\Ajax\ContactFormHandler;
use WPStarterPlugin\Ajax\PortfolioLoadMoreHandler;

/**
 * Registers all plugin AJAX action hooks for authenticated and public requests.
 */
class AjaxProvider {

	/**
	 * Registers the WordPress AJAX action hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		$loadMore = new PortfolioLoadMoreHandler();
		add_action( 'wp_ajax_wsp_portfolio_load_more', [ $loadMore, 'handle' ] );
		add_action( 'wp_ajax_nopriv_wsp_portfolio_load_more', [ $loadMore, 'handle' ] );

		$contact = new ContactFormHandler();
		add_action( 'wp_ajax_wsp_contact_form', [ $contact, 'handle' ] );
	}
}
