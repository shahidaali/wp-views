<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WP_Views_Admin_Utility
 *
 * Views Utitlity Class
 *
 * @since	1.0.0
 *
 * @param	void
 * @return	void
 */	

if( ! class_exists('WP_Views_Admin_Utility') ) :

class WP_Views_Admin_Utility {
	/**
	 * __construct
	 *
	 * Class constructor
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function __construct() {

	}

	/**
	 * admin_save_button()
	 *
	 * Admin save button
	 *
	 * @since	1.0.0
	 *
	 * @param	$post_id 
	 * @return	button html
	 */	
	public static function admin_save_button( $post_id ) {
		static $button = '';

		if ( ! empty( $button ) ) {
			echo $button;
			return;
		}

		$nonce = wp_create_nonce( 'wp-views-save-view_' . $post_id );

		$onclick = sprintf(
			"this.form._wpnonce.value = '%s';"
			. " this.form.action.value = 'save';"
			. " return true;",
			$nonce );

		$button = sprintf(
			'<input type="submit" class="button-primary" name="wp-views-save" value="%1$s" onclick="%2$s" />',
			esc_attr( __( 'Save', 'wp-views' ) ),
			$onclick );

		echo $button;
	}
}

endif; // class_exists check
