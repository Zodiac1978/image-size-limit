<?php
/**
 * Settings for Image Size Limit.
 *
 * @package Image_Size_Limit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the plugin setting and its media settings field.
 *
 * @return void
 */
function wpisl_options_init() {
	register_setting(
		'media',
		'wpisl_options',
		array(
			'type'              => 'array',
			'default'           => wpisl_get_default_options(),
			'sanitize_callback' => 'wpisl_options_validate',
		)
	);

	add_settings_field(
		'img_upload_limit',
		esc_html__( 'Maximum file size for images', 'image-size-limit' ),
		'wpisl_settings_field_img_upload_limit',
		'media',
		'uploads'
	);
}
add_action( 'admin_init', 'wpisl_options_init' );

/**
 * Return the default options.
 *
 * @return array<string,int>
 */
function wpisl_get_default_options() {
	$default_options = array(
		'img_upload_limit' => wpisl_get_plugin()->wp_limit(),
	);

	return apply_filters( 'wpisl_default_options', $default_options );
}

/**
 * Return the saved options merged with their defaults.
 *
 * @return array<string,int>
 */
function wpisl_get_options() {
	$options = get_option( 'wpisl_options', array() );

	return wp_parse_args( is_array( $options ) ? $options : array(), wpisl_get_default_options() );
}

/**
 * Render the maximum image upload size setting.
 *
 * @return void
 */
function wpisl_settings_field_img_upload_limit() {
	$options = wpisl_get_options();
	$limit   = wpisl_get_plugin()->wp_limit();
	$value   = min( absint( $options['img_upload_limit'] ), $limit );

	printf(
		'<p><input class="small-text" name="wpisl_options[img_upload_limit]" id="wpisl-limit" type="number" step="1" min="0" max="%1$d" value="%2$d" /> %3$s<br><span class="description">%4$s</span></p>',
		absint( $limit ),
		absint( $value ),
		esc_html__( 'KB', 'image-size-limit' ),
		esc_html(
			sprintf(
				/* translators: %s: Server upload limit formatted as a file size. */
				__( 'Server maximum: %s', 'image-size-limit' ),
				wpisl_get_plugin()->format_limit( $limit )
			)
		)
	);
}

/**
 * Sanitize and validate the settings form input.
 *
 * @param mixed $input Submitted setting value.
 * @return array<string,int>
 */
function wpisl_options_validate( $input ) {
	$defaults = wpisl_get_default_options();
	$limit    = wpisl_get_plugin()->wp_limit();
	$value    = $defaults['img_upload_limit'];

	if ( is_array( $input ) && isset( $input['img_upload_limit'] ) ) {
		$value = absint( wp_unslash( $input['img_upload_limit'] ) );
	}

	$output = array(
		'img_upload_limit' => min( $value, $limit ),
	);

	return apply_filters( 'wpisl_options_validate', $output, $input, $defaults );
}
