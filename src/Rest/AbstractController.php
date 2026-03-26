<?php
/**
 * Abstract base class for plugin REST controllers.
 *
 * @package WPStarterPlugin\Rest
 */

declare(strict_types=1);

namespace WPStarterPlugin\Rest;

/**
 * Provides shared namespace, permission helpers, and schema scaffolding
 * for all plugin REST API controllers.
 *
 * @extends \WP_REST_Controller
 */
abstract class AbstractController extends \WP_REST_Controller {

	/**
	 * Plugin REST namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wp-starter/v1';

	/**
	 * Registers the controller's routes. Subclasses must implement this.
	 *
	 * @return void
	 */
	abstract public function register_routes(): void;

	/**
	 * Returns the JSON schema for the resource.
	 *
	 * @return array<string, mixed>
	 */
	protected function getSchema(): array {
		return [];
	}

	/**
	 * Permission callback: checks whether the current user can edit posts.
	 *
	 * @param \WP_REST_Request<array<string, mixed>> $request Incoming request.
	 * @return bool
	 */
	protected function canEdit( \WP_REST_Request $request ): bool {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Permission callback: public read — always allow.
	 *
	 * @param \WP_REST_Request<array<string, mixed>> $request Incoming request.
	 * @return bool
	 */
	protected function canRead( \WP_REST_Request $request ): bool {
		return true;
	}

	/**
	 * Returns a standardised error response.
	 *
	 * @param string $code    Machine-readable error code.
	 * @param string $message Human-readable message.
	 * @param int    $status  HTTP status code.
	 * @return \WP_Error
	 */
	protected function error( string $code, string $message, int $status = 400 ): \WP_Error {
		return new \WP_Error( $code, $message, [ 'status' => $status ] );
	}

	/**
	 * Validates that a value is a positive integer.
	 *
	 * @param mixed $value  The value to validate.
	 * @param string $param Parameter name for the error message.
	 * @return true|\WP_Error
	 */
	protected function validatePositiveInt( mixed $value, string $param ): true|\WP_Error {
		if ( ! is_numeric( $value ) || (int) $value < 1 ) {
			return $this->error(
				'invalid_param',
				/* translators: %s: parameter name */
				sprintf( __( '%s must be a positive integer.', 'wp-starter-plugin' ), $param )
			);
		}
		return true;
	}
}
