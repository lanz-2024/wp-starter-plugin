<?php
/**
 * Content-related WordPress filter hooks.
 *
 * @package WPStarterPlugin\Hooks
 */

declare(strict_types=1);

namespace WPStarterPlugin\Hooks;

/**
 * Content-related WordPress filter hooks.
 */
class ContentFilters {

	/**
	 * Register all content filter hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_filter( 'the_content', array( $this, 'append_portfolio_link' ) );
		add_filter( 'excerpt_length', array( $this, 'set_excerpt_length' ) );
		add_filter( 'body_class', array( $this, 'add_body_classes' ) );
	}

	/**
	 * Append a "Back to Portfolio" link to portfolio CPT content.
	 *
	 * @param string $content Post content.
	 * @return string Modified content.
	 */
	public function append_portfolio_link( string $content ): string {
		if ( ! is_singular( 'portfolio' ) || ! in_the_loop() ) {
			return $content;
		}

		$link = sprintf(
			'<p class="portfolio-back-link"><a href="%s">%s</a></p>',
			esc_url( get_post_type_archive_link( 'portfolio' ) ),
			esc_html__( '← Back to Portfolio', 'wp-starter-plugin' ),
		);

		return $content . $link;
	}

	/**
	 * Set excerpt length to 25 words.
	 *
	 * @param int $length Default excerpt length.
	 * @return int New excerpt length.
	 */
	public function set_excerpt_length( int $length ): int {
		return 25;
	}

	/**
	 * Add custom CSS body classes.
	 *
	 * @param string[] $classes Existing body classes.
	 * @return string[]
	 */
	public function add_body_classes( array $classes ): array {
		if ( is_singular( 'portfolio' ) ) {
			$classes[] = 'is-portfolio-single';
		}

		if ( is_post_type_archive( 'portfolio' ) ) {
			$classes[] = 'is-portfolio-archive';
		}

		return $classes;
	}
}
