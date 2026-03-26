<?php
/**
 * Email service: HTML notifications via wp_mail.
 *
 * @package WPStarterPlugin\Services
 */

declare(strict_types=1);

namespace WPStarterPlugin\Services;

/**
 * HTML email service using wp_mail.
 */
class EmailService {

	/**
	 * Send an HTML notification email.
	 *
	 * @param string               $to      Recipient email address.
	 * @param string               $subject Email subject.
	 * @param array<string,mixed>  $data    Template variables.
	 * @return bool Whether the email was sent successfully.
	 */
	public function send_notification( string $to, string $subject, array $data ): bool {
		$body    = $this->render_template( 'notification', $data );
		$headers = [ 'Content-Type: text/html; charset=UTF-8' ];

		return (bool) wp_mail( $to, $subject, $body, $headers );
	}

	/**
	 * Render an email template.
	 *
	 * @param string               $template Template name (without .php extension).
	 * @param array<string,mixed>  $data     Template variables.
	 * @return string Rendered HTML.
	 */
	private function render_template( string $template, array $data ): string {
		$template_file = plugin_dir_path( __FILE__ ) . "../../templates/emails/{$template}.php";

		if ( ! file_exists( $template_file ) ) {
			return '';
		}

		ob_start();
		extract( $data, EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract
		include $template_file;
		return (string) ob_get_clean();
	}
}
