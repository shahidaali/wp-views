<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WP_Views_Content_Type_Post
 *
 * Views Entity Type Post Defination
 *
 * @since	1.0.0
 *
 * @param	void
 * @return	void
 */	

if( ! class_exists('WP_Views_Content_Type_Post') ) :

class WP_Views_Content_Type_Post extends WP_Views_Content_Type_Base {
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

		$this->key = 'post';
		$this->label = __( 'Posts', 'wp-views' );
		$this->base_table = 'posts';

		// Sorting Options
		$this->sorting_options = [
			'post_date:DESC' => __( 'Newest First', 'wp-views' ),
			'post_date:ASC' => __( 'Oldest First', 'wp-views' ),
			'post_title:ASC' => __( 'Title', 'wp-views' ),
		];

		// Content type fields
		$this->set_field(
			new WP_Views_Field_Numeric([
				'key' => 'ID',
				'label' => __( 'ID', 'wp-views' )
			])
		);

		$this->set_field(
			new WP_Views_Field_Text([
				'key' => 'post_title',
				'label' => __( 'Post Title', 'wp-views' )
			])
		);
	}
}

endif; // class_exists check
