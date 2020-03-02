<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WP_Views_Field_Base
 *
 * Views Numeric Field Defination
 *
 * @since	1.0.0
 *
 * @param	void
 * @return	void
 */	

if( ! class_exists('WP_Views_Field_Base') ) :

abstract class WP_Views_Field_Base {
	/** @var defination vars. */
	var $key = "";
	var $label = "";
	var $field_type = "";
	var $field_type_label = "";

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
	function __construct( $field ) {
		// Set field properties
		foreach ($field as $key => $value) {
			if( isset($this->{$key}) ) {
				$this->{$key} = $value;
			}
		}
	}

	/**
	 * set_field
	 *
	 * Set field
	 *
	 * @since	1.0.0
	 *
	 * @param	$value field value
	 * @return	Rendered field value
	 */	
	function set_field( $field ) {
		foreach ($field as $key => $value) {
			if( isset($this->{$key}) ) {
				$this->{$key} = $value;
			}
		}
	}
}

endif; // class_exists check
