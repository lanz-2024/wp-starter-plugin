<?php
/**
 * REST API controller for Portfolio items.
 *
 * @package WPStarterPlugin\Rest
 */

declare(strict_types=1);

namespace WPStarterPlugin\Rest;

use WP_REST_Request;
use WP_REST_Response;
use WP_Error;
use WPStarterPlugin\PostTypes\Portfolio as PortfolioPostType;
use WPStarterPlugin\Services\CacheManager;
use WPStarterPlugin\Services\QueryOptimizer;

/**
 * Handles CRUD operations, meta, and taxonomy filtering for portfolio items
 * under the `wp-starter/v1/portfolio` route.
 */
class PortfolioController extends AbstractController {

	/**
	 * REST resource base.
	 *
	 * @var string
	 */
	protected $rest_base = 'portfolio';

	private PortfolioPostType $portfolioPostType;
	private CacheManager $cache;
	private QueryOptimizer $queryOptimizer;

	/**
	 * Constructs the controller.
	 *
	 * @param CacheManager   $cache          Cache layer.
	 * @param QueryOptimizer $queryOptimizer Query optimiser.
	 */
	public function __construct( CacheManager $cache, QueryOptimizer $queryOptimizer ) {
		$this->cache             = $cache;
		$this->queryOptimizer    = $queryOptimizer;
		$this->portfolioPostType = new PortfolioPostType();
	}

	/**
	 * Registers REST routes for this controller.
	 *
	 * @return void
	 */
	public function register_routes(): void {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'getItems' ),
					'permission_callback' => array( $this, 'canRead' ),
					'args'                => $this->getCollectionParams(),
				),
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'createItem' ),
					'permission_callback' => array( $this, 'canEdit' ),
					'args'                => $this->getItemSchema()['properties'] ?? array(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'getItem' ),
					'permission_callback' => array( $this, 'canRead' ),
					'args'                => array( 'id' => array( 'validate_callback' => 'is_numeric' ) ),
				),
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'updateItem' ),
					'permission_callback' => array( $this, 'canEdit' ),
				),
				array(
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'deleteItem' ),
					'permission_callback' => array( $this, 'canEdit' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Returns a paginated list of portfolio items.
	 *
	 * @param WP_REST_Request<array<string, mixed>> $request REST request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function getItems( WP_REST_Request $request ): WP_REST_Response|WP_Error {
		$per_page = (int) $request->get_param( 'per_page' );
		$page     = (int) $request->get_param( 'page' );
		$skill    = sanitize_text_field( (string) ( $request->get_param( 'skill' ) ?? '' ) );
		$industry = sanitize_text_field( (string) ( $request->get_param( 'industry' ) ?? '' ) );
		$featured = $request->get_param( 'featured' );

		$cache_key = 'portfolio_list_' . md5( serialize( compact( 'per_page', 'page', 'skill', 'industry', 'featured' ) ) );

		$data = $this->cache->remember(
			$cache_key,
			function () use ( $per_page, $page, $skill, $industry, $featured ): array {
				$args = $this->queryOptimizer->buildArgs(
					PortfolioPostType::POST_TYPE,
					$per_page,
					$page,
					$skill,
					$industry,
					$featured !== null ? (bool) $featured : null
				);

				$query = new \WP_Query( $args );
				$items = array();

				foreach ( $query->posts as $post ) {
					if ( $post instanceof \WP_Post ) {
						$items[] = $this->prepareItem( $post );
					}
				}

				return array(
					'items' => $items,
					'total' => (int) $query->found_posts,
					'pages' => (int) $query->max_num_pages,
				);
			},
			300
		);

		if ( ! is_array( $data ) ) {
			$data = array(
				'items' => array(),
				'total' => 0,
				'pages' => 0,
			);
		}

		$response = new WP_REST_Response( $data['items'], 200 );
		$response->header( 'X-WP-Total', (string) $data['total'] );
		$response->header( 'X-WP-TotalPages', (string) $data['pages'] );

		return $response;
	}

	/**
	 * Returns a single portfolio item.
	 *
	 * @param WP_REST_Request<array<string, mixed>> $request REST request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function getItem( WP_REST_Request $request ): WP_REST_Response|WP_Error {
		$id   = (int) $request->get_param( 'id' );
		$post = get_post( $id );

		if ( ! $post instanceof \WP_Post || $post->post_type !== PortfolioPostType::POST_TYPE ) {
			return $this->error( 'not_found', __( 'Portfolio item not found.', 'wp-starter-plugin' ), 404 );
		}

		return new WP_REST_Response( $this->prepareItem( $post ), 200 );
	}

	/**
	 * Creates a new portfolio item.
	 *
	 * @param WP_REST_Request<array<string, mixed>> $request REST request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function createItem( WP_REST_Request $request ): WP_REST_Response|WP_Error {
		$title   = sanitize_text_field( (string) ( $request->get_param( 'title' ) ?? '' ) );
		$content = wp_kses_post( (string) ( $request->get_param( 'content' ) ?? '' ) );

		if ( $title === '' ) {
			return $this->error( 'missing_title', __( 'Title is required.', 'wp-starter-plugin' ) );
		}

		$post_id = wp_insert_post(
			array(
				'post_title'   => $title,
				'post_content' => $content,
				'post_type'    => PortfolioPostType::POST_TYPE,
				'post_status'  => 'publish',
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		$this->saveMeta( $post_id, $request );
		$this->cache->delete( 'portfolio_list_*' );

		$post = get_post( $post_id );
		if ( ! $post instanceof \WP_Post ) {
			return $this->error( 'creation_failed', __( 'Failed to retrieve created item.', 'wp-starter-plugin' ), 500 );
		}

		return new WP_REST_Response( $this->prepareItem( $post ), 201 );
	}

	/**
	 * Updates an existing portfolio item.
	 *
	 * @param WP_REST_Request<array<string, mixed>> $request REST request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function updateItem( WP_REST_Request $request ): WP_REST_Response|WP_Error {
		$id   = (int) $request->get_param( 'id' );
		$post = get_post( $id );

		if ( ! $post instanceof \WP_Post || $post->post_type !== PortfolioPostType::POST_TYPE ) {
			return $this->error( 'not_found', __( 'Portfolio item not found.', 'wp-starter-plugin' ), 404 );
		}

		$update_args = array( 'ID' => $id );

		$title = $request->get_param( 'title' );
		if ( $title !== null ) {
			$update_args['post_title'] = sanitize_text_field( (string) $title );
		}

		$content = $request->get_param( 'content' );
		if ( $content !== null ) {
			$update_args['post_content'] = wp_kses_post( (string) $content );
		}

		$result = wp_update_post( $update_args, true );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$this->saveMeta( $id, $request );
		$this->cache->delete( 'portfolio_item_' . $id );

		$post = get_post( $id );
		if ( ! $post instanceof \WP_Post ) {
			return $this->error( 'update_failed', __( 'Failed to retrieve updated item.', 'wp-starter-plugin' ), 500 );
		}

		return new WP_REST_Response( $this->prepareItem( $post ), 200 );
	}

	/**
	 * Deletes a portfolio item.
	 *
	 * @param WP_REST_Request<array<string, mixed>> $request REST request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function deleteItem( WP_REST_Request $request ): WP_REST_Response|WP_Error {
		$id    = (int) $request->get_param( 'id' );
		$force = (bool) ( $request->get_param( 'force' ) ?? false );
		$post  = get_post( $id );

		if ( ! $post instanceof \WP_Post || $post->post_type !== PortfolioPostType::POST_TYPE ) {
			return $this->error( 'not_found', __( 'Portfolio item not found.', 'wp-starter-plugin' ), 404 );
		}

		$deleted = wp_delete_post( $id, $force );
		if ( ! $deleted ) {
			return $this->error( 'delete_failed', __( 'Could not delete portfolio item.', 'wp-starter-plugin' ), 500 );
		}

		$this->cache->delete( 'portfolio_item_' . $id );

		return new WP_REST_Response(
			array(
				'deleted' => true,
				'id'      => $id,
			),
			200
		);
	}

	/**
	 * Transforms a WP_Post into the REST response shape.
	 *
	 * @param \WP_Post $post Post object.
	 * @return array<string, mixed>
	 */
	private function prepareItem( \WP_Post $post ): array {
		return array(
			'id'           => $post->ID,
			'title'        => get_the_title( $post ),
			'excerpt'      => get_the_excerpt( $post ),
			'content'      => apply_filters( 'the_content', $post->post_content ),
			'url'          => $this->portfolioPostType->getProjectUrl( $post->ID ),
			'repo_url'     => $this->portfolioPostType->getRepoUrl( $post->ID ),
			'client'       => $this->portfolioPostType->getClient( $post->ID ),
			'year'         => $this->portfolioPostType->getYear( $post->ID ),
			'featured'     => $this->portfolioPostType->isFeatured( $post->ID ),
			'technologies' => $this->portfolioPostType->getTechnologies( $post->ID ),
			'skills'       => $this->getTermNames( $post->ID, 'skill' ),
			'industries'   => $this->getTermNames( $post->ID, 'industry' ),
			'thumbnail'    => get_the_post_thumbnail_url( $post->ID, 'large' ) ?: null,
			'permalink'    => get_permalink( $post->ID ),
			'date'         => $post->post_date_gmt,
			'modified'     => $post->post_modified_gmt,
		);
	}

	/**
	 * Returns an array of term names for a post and taxonomy.
	 *
	 * @param int    $postId   Post ID.
	 * @param string $taxonomy Taxonomy slug.
	 * @return list<string>
	 */
	private function getTermNames( int $postId, string $taxonomy ): array {
		$terms = get_the_terms( $postId, $taxonomy );
		if ( ! is_array( $terms ) ) {
			return array();
		}
		return array_values( array_map( fn( \WP_Term $t ) => $t->name, $terms ) );
	}

	/**
	 * Saves meta fields from a REST request to a post.
	 *
	 * @param int                                   $postId  Post ID.
	 * @param WP_REST_Request<array<string, mixed>> $request REST request.
	 * @return void
	 */
	private function saveMeta( int $postId, WP_REST_Request $request ): void {
		$meta_map = array(
			'url'          => 'sanitize_url',
			'repo_url'     => 'sanitize_url',
			'client'       => 'sanitize_text_field',
			'year'         => 'intval',
			'featured'     => 'boolval',
			'technologies' => 'sanitize_text_field',
		);

		foreach ( $meta_map as $key => $sanitizer ) {
			$value = $request->get_param( $key );
			if ( $value !== null ) {
				$this->portfolioPostType->setMeta( $postId, $key, $sanitizer( $value ) );
			}
		}
	}

	/**
	 * Returns the collection query parameter definitions.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	private function getCollectionParams(): array {
		return array(
			'per_page' => array(
				'default'           => 10,
				'sanitize_callback' => 'absint',
				'validate_callback' => fn( mixed $v ) => is_numeric( $v ) && (int) $v > 0,
			),
			'page'     => array(
				'default'           => 1,
				'sanitize_callback' => 'absint',
				'validate_callback' => fn( mixed $v ) => is_numeric( $v ) && (int) $v > 0,
			),
			'skill'    => array( 'sanitize_callback' => 'sanitize_text_field' ),
			'industry' => array( 'sanitize_callback' => 'sanitize_text_field' ),
			'featured' => array( 'sanitize_callback' => 'rest_sanitize_boolean' ),
		);
	}

	/**
	 * Returns the JSON schema for a portfolio item.
	 *
	 * @return array<string, mixed>
	 */
	protected function getItemSchema(): array {
		return array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'portfolio',
			'type'       => 'object',
			'properties' => array(
				'id'           => array(
					'type'     => 'integer',
					'readonly' => true,
				),
				'title'        => array(
					'type'     => 'string',
					'required' => true,
				),
				'content'      => array( 'type' => 'string' ),
				'url'          => array(
					'type'   => 'string',
					'format' => 'uri',
				),
				'repo_url'     => array(
					'type'   => 'string',
					'format' => 'uri',
				),
				'client'       => array( 'type' => 'string' ),
				'year'         => array( 'type' => 'integer' ),
				'featured'     => array( 'type' => 'boolean' ),
				'technologies' => array( 'type' => 'string' ),
			),
		);
	}
}
