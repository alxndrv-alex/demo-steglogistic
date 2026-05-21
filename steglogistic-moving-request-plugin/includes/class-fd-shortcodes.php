<?php
class FD_Shortcodes {
	public static function init(): void {
		add_shortcode( 'smr-form', array( self::class, 'moving_request_form' ) );
	}

	public static function moving_request_form(): string {
		return file_get_contents( FD_SMR_PLUGIN_DIR_PATH . '/templates/html/form.html' );
	}
}
