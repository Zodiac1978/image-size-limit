<?php
/**
 * WP Image Size Limit - Options
 *
 * @since Version 1.0
 */


/**
 * Register the form setting for our wpisl_options array.
 *
 * This function is attached to the admin_init action hook.
 *
 * @since Version 1.0
 */
function wpisl_options_init() {

	// If we have no options in the database, let's add them now.
	if ( false === wpisl_get_options() ) {
		add_option( 'wpisl_options', wpisl_get_default_options() );
	}

	$args = array(
		'sanitize_callback' => 'wpisl_options_validate',
		'default'           => null,
	);
	register_setting(
		'media',         // Options group.
		'wpisl_options', // Database option, see wpisl_get_options().
		$args            // The sanitization callback, see wpisl_options_validate().
	);

	add_settings_field(
		'img_upload_limit',
		__( 'Maximum File Size for Images', 'image-size-limit' ),
		'wpisl_settings_field_img_upload_limit',
		'media',
		'uploads'
	);
}
add_action( 'admin_init', 'wpisl_options_init' );


/**
 * Returns the default options.
 *
 * @since Version 1.0
 */
function wpisl_get_default_options() {
	$wpisl           = new WP_Image_Size_Limit();
	$limit           = $wpisl->wp_limit();
	$default_options = array(
		'img_upload_limit' => $limit,
	);

	return apply_filters( 'wpisl_default_options', $default_options );
}

/**
 * Returns the options array.
 *
 * @since Version 1.0
 */
function wpisl_get_options() {
	return get_option( 'wpisl_options', wpisl_get_default_options() );
}


/**
 * Renders the Maximum Upload Size setting field.
 *
 * @since Version 1.0
 */
function wpisl_settings_field_img_upload_limit() {
	$options = wpisl_get_options();
	$wpisl   = new WP_Image_Size_Limit();
	$limit   = $wpisl->wp_limit();

		// Sanitize.
		$id = 'img_upload_limit';

	if ( isset( $options[ $id ] ) && ( $options[ $id ] < $limit ) ) {
		$value = $options[ $id ];
	}
		/*
		elseif  ( empty($options[$id])  )  {
			$value = '1000';
		} */
	else {
		$value = $limit;
	}

		$field = '<p>
			<input class="small-text" name="wpisl_options[' . $id . ']" id="wpisl-limit" type="number" step="1" min="0" value="' . $value . '" /> ' . esc_html__( 'KB', 'image-size-limit' ) . '
			<br>
			<span class="description">' . __( 'Server maximum:', 'image-size-limit' ) . ' ' . $limit . ' ' . esc_html__( 'KB', 'image-size-limit' ) . '</span>
		</p>';

	echo $field;
}

/**
 * Sanitize and validate form input. Accepts an array, return a sanitized array.
 *
 * @see wpisl_options_init()
 * @since Version 1.0
 */
function wpisl_options_validate( $input ) {
	$defaults = wpisl_get_default_options();
	$output   = $defaults;
	$wpisl    = new WP_Image_Size_Limit();
	$limit    = $wpisl->wp_limit();

	$output['img_upload_limit'] = str_replace( ',', '', $input['img_upload_limit'] );

	$output['img_upload_limit'] = absint( intval( $output['img_upload_limit'] ) );

	if ( $output['img_upload_limit'] > $limit ) {
		$output['img_upload_limit'] = $limit;
	}

	return apply_filters( 'wpisl_options_validate', $output, $input, $defaults );
}

/**
 * Set unique identifier for the upload limit reached error
 */
function unique_identifyer_admin_notices() {
	settings_errors( 'img_upload_limit' );
}
add_action( 'admin_notices', 'unique_identifyer_admin_notices' );
