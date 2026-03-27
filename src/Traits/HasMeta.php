<?php
/**
 * HasMeta trait — typed post-meta accessor helpers.
 *
 * @package WPStarterPlugin\Traits
 */

declare(strict_types=1);

namespace WPStarterPlugin\Traits;

/**
 * Provides get/set/delete helpers for post meta, namespaced under a prefix
 * defined by the consuming class.
 *
 * The consuming class MUST define the META_PREFIX class constant, e.g.:
 *   public const META_PREFIX = '_portfolio_';
 */
trait HasMeta {

	/**
	 * Returns the meta key prefix used for all meta operations.
	 *
	 * @return string
	 */
	abstract protected function getMetaPrefix(): string;

	/**
	 * Retrieves a single post meta value.
	 *
	 * @param int    $postId Post ID.
	 * @param string $key    Meta key (without prefix).
	 * @return mixed
	 */
	public function getMeta( int $postId, string $key ): mixed {
		return get_post_meta( $postId, $this->getMetaPrefix() . $key, true );
	}

	/**
	 * Retrieves all values for a repeatable post meta key.
	 *
	 * @param int    $postId Post ID.
	 * @param string $key    Meta key (without prefix).
	 * @return list<mixed>
	 */
	public function getMetaAll( int $postId, string $key ): array {
		$values = get_post_meta( $postId, $this->getMetaPrefix() . $key, false );
		return is_array( $values ) ? array_values( $values ) : array();
	}

	/**
	 * Updates a post meta value.
	 *
	 * @param int    $postId Post ID.
	 * @param string $key    Meta key (without prefix).
	 * @param mixed  $value  New value.
	 * @return void
	 */
	public function setMeta( int $postId, string $key, mixed $value ): void {
		update_post_meta( $postId, $this->getMetaPrefix() . $key, $value );
	}

	/**
	 * Deletes a post meta value.
	 *
	 * @param int    $postId Post ID.
	 * @param string $key    Meta key (without prefix).
	 * @return void
	 */
	public function deleteMeta( int $postId, string $key ): void {
		delete_post_meta( $postId, $this->getMetaPrefix() . $key );
	}
}
