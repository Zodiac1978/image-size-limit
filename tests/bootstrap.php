<?php
/** PHPUnit bootstrap with the small WordPress surface used by the plugin. */

define( 'ABSPATH', __DIR__ . '/' );
define( 'KB_IN_BYTES', 1024 );
define( 'MB_IN_BYTES', 1048576 );

$GLOBALS['wpisl_test_filters']         = array();
$GLOBALS['wpisl_test_actions']         = array();
$GLOBALS['wpisl_test_options']         = array();
$GLOBALS['wpisl_test_max_upload_size'] = 2.5 * MB_IN_BYTES;
$GLOBALS['wpisl_test_screen']          = null;

function add_filter( $tag, $callback ) {
	$GLOBALS['wpisl_test_filters'][ $tag ][] = $callback;
}

function add_action( $tag, $callback ) {
	$GLOBALS['wpisl_test_actions'][ $tag ][] = $callback;
}

function plugin_basename( $file ) {
	return basename( $file );
}

function admin_url( $path = '' ) {
	return 'https://example.test/wp-admin/' . ltrim( $path, '/' );
}

function get_option( $name, $default = false ) {
	return array_key_exists( $name, $GLOBALS['wpisl_test_options'] )
		? $GLOBALS['wpisl_test_options'][ $name ]
		: $default;
}

function wp_max_upload_size() {
	return $GLOBALS['wpisl_test_max_upload_size'];
}

function absint( $value ) {
	return abs( (int) $value );
}

function size_format( $bytes, $decimals = 0 ) {
	if ( $bytes >= MB_IN_BYTES ) {
		return rtrim( rtrim( number_format( $bytes / MB_IN_BYTES, $decimals, '.', '' ), '0' ), '.' ) . ' MB';
	}

	return rtrim( rtrim( number_format( $bytes / KB_IN_BYTES, $decimals, '.', '' ), '0' ), '.' ) . ' KB';
}

function wp_check_filetype( $filename ) {
	$extension = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
	$types     = array(
		'gif'  => 'image/gif',
		'jpeg' => 'image/jpeg',
		'jpg'  => 'image/jpeg',
		'pdf'  => 'application/pdf',
		'png'  => 'image/png',
		'webp' => 'image/webp',
	);

	return array( 'type' => $types[ $extension ] ?? false );
}

function wp_check_filetype_and_ext( $file, $filename ) {
	return wp_check_filetype( $filename );
}

function esc_url( $value ) {
	return $value;
}

function esc_html( $value ) {
	return htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' );
}

function esc_html__( $value ) {
	return $value;
}

function __( $value ) {
	return $value;
}

function get_current_screen() {
	return $GLOBALS['wpisl_test_screen'];
}

function register_setting() {}
function add_settings_field() {}

function apply_filters( $tag, $value ) {
	return $value;
}

function wp_parse_args( $args, $defaults = array() ) {
	return array_merge( $defaults, $args );
}

function wp_unslash( $value ) {
	return $value;
}

require dirname( __DIR__ ) . '/wp-image-size-limit.php';
