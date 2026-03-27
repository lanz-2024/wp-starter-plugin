<?php
/**
 * Portfolio model.
 *
 * @package WPStarterPlugin\Models
 */

declare(strict_types=1);

namespace WPStarterPlugin\Models;

use WP_Post;

/**
 * Typed model wrapping a Portfolio CPT post.
 */
readonly class Portfolio {

	/**
	 * Constructs the Portfolio model.
	 *
	 * @param int    $id     Post ID.
	 * @param string $title  Post title.
	 * @param string $status Post status.
	 * @param string $date   Post date.
	 */
	public function __construct(
		public int $id,
		public string $title,
		public string $status,
		public string $date,
	) {}

	/**
	 * Create from a WP_Post object.
	 *
	 * @param WP_Post $post WordPress post object.
	 * @return self
	 */
	public static function from_post( WP_Post $post ): self {
		return new self(
			id: $post->ID,
			title: get_the_title( $post->ID ),
			status: $post->post_status,
			date: $post->post_date,
		);
	}

	/**
	 * Convert to array for REST API responses.
	 *
	 * @return array<string,mixed>
	 */
	public function to_array(): array {
		return array(
			'id'     => $this->id,
			'title'  => $this->title,
			'status' => $this->status,
			'date'   => $this->date,
		);
	}
}
