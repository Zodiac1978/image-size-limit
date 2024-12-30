<?php
/*
Plugin Name: Image Size Limit
Plugin URI: http://wordpress.org/plugins/image-size-limit
Description: Allows setting a maximum file size for image uploads.
Author: Torsten Landsiedel, Sean Butze
Author URI: https://torstenalndsiedel.de
Version: 1.0.5
*/


define( 'WPISL_DEBUG', false );

require_once 'wpisl-options.php';

class WP_Image_Size_Limit {

	/**
	 * Construct main plugin functionality
	 */
	public function __construct() {
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_plugin_links' ) );
			add_filter( 'wp_handle_upload_prefilter', array( $this, 'error_message' ) );
	}

	/**
	 * Add plugin action links
	 *
	 * @param array $links Modified array of plugin action links.
	 */
	public function add_plugin_links( $links ) {
		return array_merge(
			array(
				'settings' => '<a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/options-media.php?settings-updated=true#wpisl-limit">' . __( 'Settings', 'image-size-limit' ) . '</a>',
			),
			$links
		);
	}

	/**
	 * Get image upload limit from settings page
	 *
	 * @return integer Upload limit in KB
	 */
	public function get_limit() {
		$option = get_option( 'wpisl_options' );

		if ( isset( $option['img_upload_limit'] ) ) {
			$limit = $option['img_upload_limit'];
		} else {
			$limit = $this->wp_limit();
		}

		return $limit;
	}

	/**
	 * Output image upload limit in KB (or MB if above 1 MB)
	 *
	 * @return integer Image upload limit from plugin settings in KB (or MB if above 1 MB)
	 */
	public function output_limit() {
		$limit        = $this->get_limit();
		$limit_output = $limit;
		$mblimit      = $limit / 1000;

		if ( $limit >= 1000 ) {
			$limit_output = $mblimit;
		}

		return $limit_output;
	}

	/**
	 * Maximal upload limit read from WordPress (=server settings)
	 *
	 * @return integer Image upload limit from WordPress in KB (or MB if above 1 MB)
	 */
	public function wp_limit() {
		$output = wp_max_upload_size();
		$output = round( $output );
		$output = $output / 1000000; // Convert to megabytes.
		$output = round( $output );
		$output = $output * 1000; // Convert to kilobytes.

		return (int) $output; // round returns a float (although precision default is 0), therefore casting to integer.
	}

	/**
	 * Unit of limit (KB or MB)
	 *
	 * @return string KB (or MB if above 1 MB)
	 */
	public function limit_unit() {
		$limit = $this->get_limit();

		if ( $limit < 1000 ) {
			return esc_html__( 'KB', 'image-size-limit' );
		} else {
			return esc_html__( 'MB', 'image-size-limit' );
		}
	}

	/**
	 * Return error message if image upload limit is reached
	 *
	 * @param  array $file  An array of data for a single file.
	 * @return array        Array for file with added error message if above upload limit.
	 */
	public function error_message( $file ) {
		$size         = $file['size'];
		$size         = $size / 1024;
		$type         = $file['type'];
		$is_image     = strpos( $type, 'image' );
		$limit        = $this->get_limit();
		$limit_output = $this->output_limit();
		$unit         = $this->limit_unit();

		if ( ( $size > $limit ) && ( false !== $is_image ) ) {
			$file['error'] = 'Image files must be smaller than ' . $limit_output . $unit;
			if ( WPISL_DEBUG ) {
				$file['error'] .= ' [ filesize = ' . $size . ', limit =' . $limit . ' ]';
			}
		}
		return $file;
	}

	/**
	 * Load CSS for styling the error message
	 */
	public function load_styles() {
		$limit        = $this->get_limit();
		$limit_output = $this->output_limit();
		$mblimit      = $limit / 1000;
		$wplimit      = $this->wp_limit();
		$unit         = $this->limit_unit();

		?>
		<!-- .Custom Max Upload Size -->
		<style type="text/css">
		.after-file-upload {
			display: none;
		}
		<?php if ( $limit < $wplimit ) : ?>
		.upload-flash-bypass:after {
			content: 'Maximum image size: <?php echo absint( $limit_output ) . $unit; ?>.';
			display: block;
			margin: 15px 0;
		}
		<?php endif; ?>

		</style>
		<!-- END Custom Max Upload Size -->
		<?php
	}


}
$image_size_limit = new WP_Image_Size_Limit();
add_action( 'admin_head', array( $image_size_limit, 'load_styles' ) );
