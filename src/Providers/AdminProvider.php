<?php
/**
 * Service provider for admin UI registration.
 *
 * @package WPStarterPlugin\Providers
 */

declare(strict_types=1);

namespace WPStarterPlugin\Providers;

use WPStarterPlugin\Admin\AdminColumns;
use WPStarterPlugin\Admin\AdminNotices;
use WPStarterPlugin\Admin\DashboardWidget;
use WPStarterPlugin\Admin\MetaBoxes;
use WPStarterPlugin\Admin\SettingsPage;

/**
 * Registers admin pages, meta boxes, dashboard widgets, and custom columns.
 */
class AdminProvider {

	/**
	 * Registers all admin-side hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		if ( ! is_admin() ) {
			return;
		}

		( new SettingsPage() )->register();
		( new MetaBoxes() )->register();
		( new AdminColumns() )->register();
		( new DashboardWidget() )->register();
		( new AdminNotices() )->register();
	}
}
