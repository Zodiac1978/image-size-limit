<?php
/**
 * Plugin Name: Image Size Limit
 * Plugin URI:  https://wordpress.org/plugins/image-size-limit/
 * Description: Allows setting a maximum file size for image uploads.
 * Author:      Torsten Landsiedel, Sean Butze
 * Author URI:  https://torstenlandsiedel.de
 * Version:     1.1.0
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Text Domain: image-size-limit
 * Domain Path: /languages
 * License:     GPL-2.0-or-later
 *
 * @package Image_Size_Limit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WPISL_PLUGIN_FILE', __FILE__ );

require_once __DIR__ . '/includes/class-wp-image-size-limit.php';

/**
 * Return the shared plugin instance.
 *
 * @return WP_Image_Size_Limit
 */
function wpisl_get_plugin() {
	static $plugin = null;

	if ( null === $plugin ) {
		$plugin = new WP_Image_Size_Limit();
	}

	return $plugin;
}

require_once __DIR__ . '/wpisl-options.php';

$image_size_limit = wpisl_get_plugin();
$image_size_limit->register_hooks();
