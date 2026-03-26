<?php
/**
 * Service provider for REST API controller registration.
 *
 * @package WPStarterPlugin\Providers
 */

declare(strict_types=1);

namespace WPStarterPlugin\Providers;

use WPStarterPlugin\Container;
use WPStarterPlugin\Rest\PortfolioController;
use WPStarterPlugin\Rest\SettingsController;
use WPStarterPlugin\Rest\StatsController;
use WPStarterPlugin\Services\CacheManager;
use WPStarterPlugin\Services\QueryOptimizer;

/**
 * Registers plugin REST controllers on the `rest_api_init` action.
 */
class RestApiProvider {

	private Container $container;

	/**
	 * Constructs the provider.
	 *
	 * @param Container $container DI container.
	 */
	public function __construct( Container $container ) {
		$this->container = $container;
	}

	/**
	 * Hooks into WordPress to register REST routes.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'rest_api_init', [ $this, 'registerControllers' ] );
	}

	/**
	 * Instantiates and registers all REST controllers.
	 *
	 * @return void
	 */
	public function registerControllers(): void {
		$cache     = $this->container->make( CacheManager::class );
		$optimizer = $this->container->make( QueryOptimizer::class );

		( new PortfolioController( $cache, $optimizer ) )->register_routes();
		( new SettingsController() )->register_routes();
		( new StatsController( $cache ) )->register_routes();
	}
}
