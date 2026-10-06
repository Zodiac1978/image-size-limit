<?php
/**
 * Main plugin class.
 *
 * @package Image_Size_Limit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enforces the configured image upload size limit.
 */
class WP_Image_Size_Limit {

	/**
	 * Whether the WordPress hooks have already been registered.
	 *
	 * @var bool
	 */
	private $hooks_registered = false;

	/**
	 * Register the plugin hooks exactly once.
	 *
	 * @return void
	 */
	public function register_hooks() {
		if ( $this->hooks_registered ) {
			return;
		}

		add_filter( 'plugin_action_links_' . plugin_basename( WPISL_PLUGIN_FILE ), array( $this, 'add_plugin_links' ) );
		add_filter( 'wp_handle_upload_prefilter', array( $this, 'validate_upload' ) );
		add_action( 'admin_notices', array( $this, 'render_upload_notice' ) );

		$this->hooks_registered = true;
	}

	/**
	 * Add a link to the media settings.
	 *
	 * @param array<string,string> $links Plugin action links.
	 * @return array<string,string>
	 */
	public function add_plugin_links( $links ) {
		$settings_link = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( admin_url( 'options-media.php#wpisl-limit' ) ),
			esc_html__( 'Settings', 'image-size-limit' )
		);

		return array_merge( array( 'settings' => $settings_link ), $links );
	}

	/**
	 * Get the configured image upload limit.
	 *
	 * @return int Upload limit in KiB.
	 */
	public function get_limit() {
		$option = get_option( 'wpisl_options', array() );

		if ( is_array( $option ) && isset( $option['img_upload_limit'] ) ) {
			return min( absint( $option['img_upload_limit'] ), $this->wp_limit() );
		}

		return $this->wp_limit();
	}

	/**
	 * Format a limit using WordPress' localized size formatter.
	 *
	 * @param int|null $limit Upload limit in KiB. Defaults to the saved limit.
	 * @return string
	 */
	public function format_limit( $limit = null ) {
		if ( null === $limit ) {
			$limit = $this->get_limit();
		}

		return size_format( absint( $limit ) * KB_IN_BYTES, 1 );
	}

	/**
	 * Return the numeric limit in KB or MB for backward compatibility.
	 *
	 * @return int|float
	 */
	public function output_limit() {
		$limit = $this->get_limit();

		return $limit >= 1000 ? $limit / 1000 : $limit;
	}

	/**
	 * Get the server upload limit exposed by WordPress.
	 *
	 * @return int Server upload limit in KiB.
	 */
	public function wp_limit() {
		return max( 0, (int) floor( wp_max_upload_size() / KB_IN_BYTES ) );
	}

	/**
	 * Return the legacy display unit for integrations using this method.
	 *
	 * @return string
	 */
	public function limit_unit() {
		return $this->get_limit() < 1000
			? esc_html__( 'KB', 'image-size-limit' )
			: esc_html__( 'MB', 'image-size-limit' );
	}

	/**
	 * Determine whether an upload is an image using WordPress' file checks.
	 *
	 * @param array<string,mixed> $file Upload data.
	 * @return bool
	 */
	public function is_image( $file ) {
		$name = isset( $file['name'] ) ? (string) $file['name'] : '';
		$type = '';

		if ( ! empty( $file['tmp_name'] ) && is_readable( $file['tmp_name'] ) ) {
			$checked = wp_check_filetype_and_ext( $file['tmp_name'], $name );
			$type    = (string) $checked['type'];
		}

		if ( '' === $type && '' !== $name ) {
			$checked = wp_check_filetype( $name );
			$type    = (string) $checked['type'];
		}

		return 0 === strpos( $type, 'image/' );
	}

	/**
	 * Reject image uploads that exceed the configured limit.
	 *
	 * @param array<string,mixed> $file Upload data.
	 * @return array<string,mixed>
	 */
	public function validate_upload( $file ) {
		$size_bytes  = isset( $file['size'] ) ? max( 0, (int) $file['size'] ) : 0;
		$limit_kib   = $this->get_limit();
		$limit_bytes = $limit_kib * KB_IN_BYTES;

		if ( $this->is_image( $file ) && $size_bytes > $limit_bytes ) {
			$file['error'] = sprintf(
				/* translators: %s: Maximum allowed image file size, for example 1.5 MB. */
				esc_html__( 'Image files must be smaller than %s.', 'image-size-limit' ),
				$this->format_limit( $limit_kib )
			);

			if ( defined( 'WPISL_DEBUG' ) && true === WPISL_DEBUG ) {
				$file['error'] .= sprintf( ' [filesize=%d, limit=%d]', $size_bytes, $limit_bytes );
			}
		}

		return $file;
	}

	/**
	 * Backward-compatible alias for the former upload callback.
	 *
	 * @param array<string,mixed> $file Upload data.
	 * @return array<string,mixed>
	 */
	public function error_message( $file ) {
		return $this->validate_upload( $file );
	}

	/**
	 * Show the image-specific limit on WordPress media screens.
	 *
	 * @return void
	 */
	public function render_upload_notice() {
		$screen = get_current_screen();

		if ( ! $screen || ! in_array( $screen->id, array( 'media', 'upload' ), true ) ) {
			return;
		}

		if ( $this->get_limit() >= $this->wp_limit() ) {
			return;
		}

		printf(
			'<div class="notice notice-info inline"><p>%s</p></div>',
			esc_html(
				sprintf(
					/* translators: %s: Maximum allowed image file size, for example 1.5 MB. */
					__( 'Maximum image file size: %s.', 'image-size-limit' ),
					$this->format_limit()
				)
			)
		);
	}
}
