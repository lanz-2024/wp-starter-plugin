<?php
/**
 * Service provider for widget registration.
 *
 * @package WPStarterPlugin\Providers
 */

declare(strict_types=1);

namespace WPStarterPlugin\Providers;

use WPStarterPlugin\Widgets\RecentPortfolioWidget;

/**
 * Registers plugin widgets on the `widgets_init` action.
 */
class WidgetProvider {

	/**
	 * Registers the `widgets_init` action hook.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'widgets_init', [ $this, 'registerWidgets' ] );
	}

	/**
	 * Registers all plugin widget classes with WordPress.
	 *
	 * @return void
	 */
	public function registerWidgets(): void {
		register_widget( RecentPortfolioWidget::class );
	}
}
