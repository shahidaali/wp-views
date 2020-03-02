<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WP_Views_Base
 *
 * Base Class
 *
 * @since	1.0.0
 *
 * @param	void
 * @return	void
 */	
if( ! class_exists('WP_Views_Base') ) :

class WP_Views_Base {
	
	/** @var string The plugin version number. */
	var $version = '1.0.0';
	
	/** @var array The plugin settings array. */
	var $settings = array();
	
	/** @var array content types array */
	var $content_types = array();
	
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

		// Set settings
		$options = get_option('wp_views_settings') ? get_option('wp_views_settings') : [];
		$this->set_settings([
			'plugin_enabled' => 1,
			'show_in_excerpt' => 1,
		], $options );

		// WP Viws Post Type
		$this->register_post_type();
		add_filter( 'map_meta_cap', array( $this, 'map_meta_cap' ) , 10, 4 );

		// WP Viws Content Types
		$this->register_content_types();
	}

	/**
	 * register_post_type
	 *
	 * register wp views post type to save views
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function register_post_type() {
		register_post_type( WP_VIEWS_POST_TYPE, array(
			'labels' => array(
				'name' => __( 'WP Views', 'wp_views' ),
				'singular_name' => __( 'WP Views View', 'wp_views' ),
			),
			'rewrite' => false,
			'query_var' => false,
			'public' => false,
			'capability_type' => 'page',
			'capabilities' => array(
				'edit_post' => 'wp_views_edit_view',
				'read_post' => 'wp_views_read_view',
				'delete_post' => 'wp_views_delete_view',
				'edit_posts' => 'wp_views_edit_views',
				'edit_others_posts' => 'wp_views_edit_views',
				'publish_posts' => 'wp_views_edit_views',
				'read_private_posts' => 'wp_views_edit_views',
			),
		) );
	}

	/**
	 * map_meta_cap
	 *
	 * map post type meta caps
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function map_meta_cap( $caps, $cap, $user_id, $args ) {
		$meta_caps = array(
			'wp_views_edit_view' => 'publish_pages',
			'wp_views_edit_views' => 'publish_pages',
			'wp_views_read_view' => 'edit_posts',
			'wp_views_read_views' => 'edit_posts',
			'wp_views_delete_view' => 'publish_pages',
			'wp_views_delete_views' => 'publish_pages',
			'wp_views_manage_settings' => 'manage_options',
			'wp_views_submit' => 'read',
		);

		$meta_caps = apply_filters( 'wp_views_map_meta_cap', $meta_caps );

		$caps = array_diff( $caps, array_keys( $meta_caps ) );

		if ( isset( $meta_caps[$cap] ) ) {
			$caps[] = $meta_caps[$cap];
		}

		return $caps;
	}

	/**
	 * register_content_types
	 *
	 * Register available content types
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function register_content_types() {
		$this->content_types[ 'post' ] = new WP_Views_Content_Type_Post();
		$this->content_types[ 'taxonomy_term' ] = new WP_Views_Content_Type_Term();
	}

	/**
	 * set_settings
	 *
	 * Update plugin settings
	 *
	 * @since	1.0.0
	 *
	 * @param	$settings: default settings, $options: options to merge
	 * @return	void
	 */	
	function set_settings($settings, $options) {
		// Merge plugin settings and default settings
		$settings = array_merge($settings, $options);

		// Filter and set Settings
		$this->settings = apply_filters('wp_views_settings', $settings);
	}

	/**
	 * get_setting
	 *
	 * Get setting
	 *
	 * @since	1.0.0
	 *
	 * @param	$key: settings key, $default: default value
	 * @return	Setting value
	 */	
	function get_setting($key, $default = null) {
		return isset($this->settings[$key]) ? $this->settings[$key] : $default;
	}
	
	/**
	 * get_data
	 *
	 * Get value from array
	 *
	 * @since	1.0.0
	 *
	 * @param	$data: data array, $key: data key, $default: default value
	 * @return	data value
	 */	
	function get_data($data, $key, $default = null) {
		return isset($data[$key]) ? $data[$key] : $default;
	}
	
	/**
	 * is_checked
	 *
	 * Checked checkbox
	 *
	 * @since	1.0.0
	 *
	 * @param	$value: checked value, $compare: checkbox value to compare
	 * @return	checked attribute
	 */	
	function is_checked($value, $compare = 1) {
		return ($value == $compare) ? 'checked="checked"' : '';
	}

	/**
	 * select_options
	 *
	 * Array to select options
	 *
	 * @since	1.0.0
	 *
	 * @param	$rows: array values, $selected_option: selected option, $use_key: usey keys for values
	 * @return	checked attribute
	 */	
	function select_options($rows, $selected_option = null, $empty_lable = "", $use_key = true) {
	    if( !is_array($rows) ) return;

	    $options = "";

	    // Selected value to array for multiple values
	    if($selected_option && !is_array($selected_option)) {
	        $selected_option = array($selected_option);
	    }

	    // Empty label
	    if( $empty_lable != "" ) {
	        $options .= "<option value=\"\">{$empty_lable}</option>";
	    }

	    // Creaye options from array
	    foreach ($rows as $key => $value) {
	        $value_item = ($use_key) ? $key : $value;
	        $selected = (!empty($selected_option) && in_array($value_item, $selected_option)) ? 'selected="selected"' : "";

	        $options .= "<option value=\"{$value_item}\" {$selected}>{$value}</option>";
	    }
	    return $options;
	}	

	/**
	 * load_view
	 *
	 * Load view from post 
	 *
	 * @since	1.0.0
	 *
	 * @param	$post: view post from wp-views-post-type
	 * @return	View instance
	 */	
	function load_view( $post ) {
		$view = new WP_Views_View($post);
		return $view;
	}
}

endif; // class_exists check
