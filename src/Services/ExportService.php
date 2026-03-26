<?php
/**
 * Export service: CSV and JSON export of CPT data.
 *
 * @package WPStarterPlugin\Services
 */

declare(strict_types=1);

namespace WPStarterPlugin\Services;

use WP_Query;

/**
 * Handles CSV and JSON export of CPT data.
 */
class ExportService {

	/**
	 * Export portfolio items as CSV.
	 *
	 * @param int $limit Maximum items to export.
	 * @return string CSV content.
	 */
	public function export_portfolio_csv( int $limit = 500 ): string {
		$items = $this->get_portfolio_items( $limit );
		$rows  = [ implode( ',', [ 'ID', 'Title', 'Status', 'Date' ] ) ];

		foreach ( $items as $item ) {
			$rows[] = implode( ',', [
				$item->ID,
				'"' . addslashes( get_the_title( $item->ID ) ) . '"',
				$item->post_status,
				$item->post_date,
			] );
		}

		return implode( "\n", $rows );
	}

	/**
	 * Export portfolio items as JSON.
	 *
	 * @param int $limit Maximum items to export.
	 * @return string JSON content.
	 */
	public function export_portfolio_json( int $limit = 500 ): string {
		$items = $this->get_portfolio_items( $limit );
		$data  = array_map(
			fn( \WP_Post $p ) => [
				'id'     => $p->ID,
				'title'  => get_the_title( $p->ID ),
				'status' => $p->post_status,
				'date'   => $p->post_date,
			],
			$items,
		);

		$json = wp_json_encode( $data, JSON_PRETTY_PRINT );
		return $json !== false ? $json : '[]';
	}

	/**
	 * Fetch portfolio posts.
	 *
	 * @param int $limit Maximum number of posts to fetch.
	 * @return \WP_Post[]
	 */
	private function get_portfolio_items( int $limit ): array {
		$query = new WP_Query( [
			'post_type'      => 'portfolio',
			'posts_per_page' => $limit,
			'post_status'    => 'publish',
			'no_found_rows'  => true,
		] );

		return $query->posts;
	}
}
