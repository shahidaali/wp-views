<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WP_Views_Content_Type_Base
 *
 * Views Entity Type Post Defination
 *
 * @since	1.0.0
 *
 * @param	void
 * @return	void
 */	

if( ! class_exists('WP_Views_Content_Type_Base') ) :

abstract class WP_Views_Content_Type_Base {
	/** @var defination vars. */
	var $key = "";
	var $label = "";
	var $base_table = "";
	var $fields = [];
	var $sorting_options = [];

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
	 * set_field
	 *
	 * Set field
	 *
	 * @since	1.0.0
	 *
	 * @param   $field: field defination
	 * @return	post object
	 */	
	function set_field( $field ) {
		$this->fields[ $field->key ] = $field;
	}
}

endif; // class_exists check
