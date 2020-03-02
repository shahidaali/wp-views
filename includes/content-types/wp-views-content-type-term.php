<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WP_Views_Content_Type_Term
 *
 * Views Entity Type Post Defination
 *
 * @since	1.0.0
 *
 * @param	void
 * @return	void
 */	

if( ! class_exists('WP_Views_Content_Type_Term') ) :

class WP_Views_Content_Type_Term extends WP_Views_Content_Type_Base {
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
		parent::__construct();

		$this->key = 'taxonomy_term';
		$this->label = __( 'Taxonomy Terms', 'wp-views' );
		$this->base_table = 'terms';

		// Content type fields
		$this->set_field(
			new WP_Views_Field_Numeric([
				'key' => 'term_id',
				'label' => __( 'Term ID', 'wp-views' )
			])
		);
		$this->set_field(
			new WP_Views_Field_Text([
				'key' => 'name',
				'label' => __( 'Term Name', 'wp-views' )
			])
		);
		$this->set_field(
			new WP_Views_Field_Text([
				'key' => 'slug',
				'label' => __( 'Term Slug', 'wp-views' )
			])
		);
	}
}

endif; // class_exists check
