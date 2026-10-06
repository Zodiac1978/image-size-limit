<?php

use PHPUnit\Framework\TestCase;

final class ImageSizeLimitTest extends TestCase {

	/** @var WP_Image_Size_Limit */
	private $plugin;

	protected function setUp(): void {
		$this->plugin                          = wpisl_get_plugin();
		$GLOBALS['wpisl_test_options']         = array();
		$GLOBALS['wpisl_test_max_upload_size'] = 2.5 * MB_IN_BYTES;
	}

	public function test_hooks_are_registered_only_once(): void {
		$this->plugin->register_hooks();
		wpisl_options_init();

		$this->assertCount( 1, $GLOBALS['wpisl_test_filters']['wp_handle_upload_prefilter'] );
		$this->assertCount( 1, $GLOBALS['wpisl_test_filters']['plugin_action_links_wp-image-size-limit.php'] );
	}

	public function test_server_limit_preserves_fractional_megabytes(): void {
		$this->assertSame( 2560, $this->plugin->wp_limit() );
	}

	public function test_saved_limit_is_capped_at_server_limit(): void {
		$GLOBALS['wpisl_test_options']['wpisl_options'] = array( 'img_upload_limit' => 4000 );

		$this->assertSame( 2560, $this->plugin->get_limit() );
	}

	public function test_image_is_detected_by_filename_when_browser_mime_is_empty(): void {
		$file = array( 'name' => 'photo.jpg', 'type' => '', 'size' => 100 );

		$this->assertTrue( $this->plugin->is_image( $file ) );
	}

	public function test_browser_mime_does_not_turn_a_pdf_into_an_image(): void {
		$file = array( 'name' => 'document.pdf', 'type' => 'image/jpeg', 'size' => 100 );

		$this->assertFalse( $this->plugin->is_image( $file ) );
	}

	public function test_oversized_image_is_rejected(): void {
		$GLOBALS['wpisl_test_options']['wpisl_options'] = array( 'img_upload_limit' => 1536 );
		$file = array( 'name' => 'photo.png', 'type' => '', 'size' => ( 1536 * KB_IN_BYTES ) + 1 );

		$result = $this->plugin->validate_upload( $file );

		$this->assertSame( 'Image files must be smaller than 1.5 MB.', $result['error'] );
	}

	public function test_file_at_limit_is_allowed(): void {
		$GLOBALS['wpisl_test_options']['wpisl_options'] = array( 'img_upload_limit' => 1536 );
		$file = array( 'name' => 'photo.webp', 'type' => '', 'size' => 1536 * KB_IN_BYTES );

		$result = $this->plugin->validate_upload( $file );

		$this->assertArrayNotHasKey( 'error', $result );
	}

	public function test_setting_is_sanitized_and_capped(): void {
		$this->assertSame(
			array( 'img_upload_limit' => 2560 ),
			wpisl_options_validate( array( 'img_upload_limit' => '9999' ) )
		);
	}
}
