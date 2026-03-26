<?php
/**
 * Main Plugin bootstrap class.
 *
 * @package WPStarterPlugin
 */

declare(strict_types=1);

namespace WPStarterPlugin;

use WPStarterPlugin\Providers\AdminProvider;
use WPStarterPlugin\Providers\AjaxProvider;
use WPStarterPlugin\Providers\BlockProvider;
use WPStarterPlugin\Providers\CronProvider;
use WPStarterPlugin\Providers\PostTypeProvider;
use WPStarterPlugin\Providers\RestApiProvider;
use WPStarterPlugin\Providers\ShortcodeProvider;
use WPStarterPlugin\Providers\TaxonomyProvider;

/**
 * Plugin bootstrap: builds the DI container, registers providers, and
 * wires activation / deactivation hooks.
 */
class Plugin {

	private Container $container;

	/**
	 * Ordered list of service providers to boot.
	 *
	 * @var list<class-string>
	 */
	private array $providers = [
		PostTypeProvider::class,
		TaxonomyProvider::class,
		RestApiProvider::class,
		AdminProvider::class,
		AjaxProvider::class,
		CronProvider::class,
		ShortcodeProvider::class,
		BlockProvider::class,
	];

	/**
	 * Constructs the Plugin and wires core service bindings.
	 */
	public function __construct() {
		$this->container = new Container();
		$this->registerServices();
	}

	/**
	 * Binds core services into the container.
	 *
	 * @return void
	 */
	private function registerServices(): void {
		$this->container->singleton(
			Services\CacheManager::class,
			fn() => new Services\CacheManager()
		);

		$this->container->singleton(
			Services\QueryOptimizer::class,
			fn( Container $c ) => new Services\QueryOptimizer( $c->make( Services\CacheManager::class ) )
		);
	}

	/**
	 * Boots all service providers on the `plugins_loaded` action.
	 *
	 * @return void
	 */
	public function boot(): void {
		if ( ! \is_blog_installed() ) {
			return;
		}

		foreach ( $this->providers as $providerClass ) {
			$provider = $this->container->make( $providerClass );
			$provider->register();
		}

		if ( defined( 'WP_CLI' ) && \WP_CLI ) {
			\WP_CLI::add_command( 'starter', CLI\SeedCommand::class );
		}

		\load_plugin_textdomain(
			'wp-starter-plugin',
			false,
			dirname( plugin_basename( WP_STARTER_PLUGIN_FILE ) ) . '/languages'
		);
	}

	/**
	 * Runs on plugin activation: registers CPTs and flushes rewrite rules.
	 *
	 * @return void
	 */
	public function activate(): void {
		$this->bootProviders();
		\flush_rewrite_rules();

		if ( ! \get_option( 'wp_starter_plugin_version' ) ) {
			\update_option( 'wp_starter_plugin_version', WP_STARTER_PLUGIN_VERSION );
		}
	}

	/**
	 * Runs on plugin deactivation: clears cron and rewrite rules.
	 *
	 * @return void
	 */
	public function deactivate(): void {
		\flush_rewrite_rules();
		\wp_clear_scheduled_hook( 'wp_starter_cleanup' );
		\wp_clear_scheduled_hook( 'wp_starter_sync' );
	}

	/**
	 * Instantiates each provider and calls boot() if available.
	 *
	 * @return void
	 */
	private function bootProviders(): void {
		foreach ( $this->providers as $providerClass ) {
			$provider = $this->container->make( $providerClass );
			if ( method_exists( $provider, 'boot' ) ) {
				$provider->boot();
			}
		}
	}

	/**
	 * Returns the plugin's DI container.
	 *
	 * @return Container
	 */
	public function getContainer(): Container {
		return $this->container;
	}
}
