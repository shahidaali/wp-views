<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WP_Views_Front
 *
 * Frontend Class
 *
 * @since	1.0.0
 *
 * @param	void
 * @return	void
 */	
if( ! class_exists('WP_Views_Front') ) :

class WP_Views_Front extends WP_Views_Base {

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
	}
	
	/**
	 * init
	 *
	 * Class init
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function init() {
		// Show links only if plugin is enabled
		if( $this->get_setting( 'plugin_enabled', true ) ) {
			// Filter
			add_filter( 'the_content', array( $this, 'filter_content' ) );

			// Check if link enabled for excerpts
			if( $this->get_setting( 'show_in_excerpt', true ) ) {
				add_filter( 'the_excerpt', array( $this, 'filter_content' ) );
			}
		}
	}

	/**
	 * filter_content
	 *
	 * the_content callback
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function filter_content( $content ) {
		global $post;

		// Get meta values
	    $keyword_link = $this->get_formated_link( $post->ID );

	    // append Link in content
	    if( $keyword_link ) { 
	    	$content .= $keyword_link;
	    }
	    
	    return $content;
	}
}

endif; // class_exists check
