<?php
/**
 * Service provider for shortcode registration.
 *
 * @package WPStarterPlugin\Providers
 */

declare(strict_types=1);

namespace WPStarterPlugin\Providers;

use WPStarterPlugin\Shortcodes\FaqShortcode;
use WPStarterPlugin\Shortcodes\PortfolioShortcode;
use WPStarterPlugin\Shortcodes\TestimonialsShortcode;

/**
 * Registers [wsp_portfolio], [wsp_testimonials], and [wsp_faq] shortcodes.
 */
class ShortcodeProvider {

	/**
	 * Registers all plugin shortcodes.
	 *
	 * @return void
	 */
	public function register(): void {
		add_shortcode( 'wsp_portfolio', [ new PortfolioShortcode(), 'render' ] );
		add_shortcode( 'wsp_testimonials', [ new TestimonialsShortcode(), 'render' ] );
		add_shortcode( 'wsp_faq', [ new FaqShortcode(), 'render' ] );
	}
}
