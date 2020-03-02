<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WP_Views_Field_Numeric
 *
 * Views Numeric Field Defination
 *
 * @since	1.0.0
 *
 * @param	void
 * @return	void
 */	

if( ! class_exists('WP_Views_Field_Numeric') ) :

class WP_Views_Field_Numeric extends WP_Views_Field_Base {

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
		$field = array_merge( $field, [
			'field_type' => 'numeric',
			'field_type_label' => __( 'Numeric' ),
		]);
		parent::__construct( $field );
	}

	/**
	 * render
	 *
	 * Render Field Value
	 *
	 * @since	1.0.0
	 *
	 * @param	$value field value
	 * @return	Rendered field value
	 */	
	function render( $value ) {
		return $value;
	}
}

endif; // class_exists check
