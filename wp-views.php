<?php
/*
Plugin Name: WP Views
Plugin URI: http://connectpx.com/
Description: This plugin will provide backend to create views and displays for posts, categroies, taxonomies and terms.
Version: 1.0.0
Author: ConnectPX
Author URI: http://connectpx.com/
Text Domain: wp_views
Domain Path: /lang
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WP_Views
 *
 * Main Class
 *
 * @since	1.0.0
 *
 * @param	void
 * @return	void
 */	

if( ! class_exists('WP_Views') ) :

class WP_Views {
	/** @var instance, front instance. */
	var $front = null;

	/** @var instance, admin instance. */
	var $admin = null;

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
	 * init()
	 *
	 * Init WP Views
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function init() {
		// Load constants
		$this->constants();

		// Load includes
		$this->includes();
		
		// Load admin
		$this->load_admin();

		// Load front
		$this->load_front();
	}

	/**
	 * load_admin
	 *
	 * Load plugin admin
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function load_admin() {
		// Init admin
		if( is_admin() ) {
			$admin = new WP_Views_Admin();
			$admin->init();
			$this->admin = $admin;
		}
	}

	/**
	 * load_front
	 *
	 * Load plugin front
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function load_front() {
		// Init front
		$front = new WP_Views_Front();
		$front->init();
		$this->front = $front;
	}

	/**
	 * constants
	 *
	 * define constants
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function constants() {
		// Define constants.
		if(!defined('WP_VIEWS_VERSION')) {
			define( 'WP_VIEWS_VERSION', '1.0.0' );
		}
		if(!defined('WP_VIEWS_PATH')) {
			define( 'WP_VIEWS_PATH', plugin_dir_path( __FILE__ ) );
		}
		if(!defined('WP_VIEWS_URL')) {
			define( 'WP_VIEWS_URL', plugins_url( '/', __FILE__ ) );
		}
		if(!defined('WP_VIEWS_BASENAME')) {
			define( 'WP_VIEWS_BASENAME', plugin_basename( __FILE__ ) );
		}
		if(!defined('WP_VIEWS_POST_TYPE')) {
			define( 'WP_VIEWS_POST_TYPE', 'wp_views_post' );
		}		
	}

	/**
	 * includes
	 *
	 * include required files
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function includes() {
		// Include utility functions.
		include_once( WP_VIEWS_PATH . 'includes/wp-views-functions.php');

		// Include utility class.
		include_once( WP_VIEWS_PATH . 'includes/wp-views-utility.php');

		// Field Defination
		include_once( WP_VIEWS_PATH . 'includes/fields/wp-views-field-base.php' );
		include_once( WP_VIEWS_PATH . 'includes/fields/wp-views-field-numeric.php' );
		include_once( WP_VIEWS_PATH . 'includes/fields/wp-views-field-text.php' );

		// Content Types Defination
		include_once( WP_VIEWS_PATH . 'includes/content-types/wp-views-content-type-base.php' );
		include_once( WP_VIEWS_PATH . 'includes/content-types/wp-views-content-type-post.php' );
		include_once( WP_VIEWS_PATH . 'includes/content-types/wp-views-content-type-term.php' );

		// Include base class
		include_once( WP_VIEWS_PATH . 'wp-views-base.php');

		// Include view singleton class
		include_once( WP_VIEWS_PATH . 'includes/wp-views-view.php');

		// Include admin class
		include_once( WP_VIEWS_PATH . 'admin/wp-views-admin.php');

		// Include front class
		include_once( WP_VIEWS_PATH . 'wp-views-front.php');
	}
}

/*
 * WP_Views
 *
 * The main function responsible for returning the one true WP_Views Instance to functions everywhere.
 * Use this function like you would a global variable, except without needing to declare the global.
 *
 * Example: <?php $WP_Views = WP_Views(); ?>
 *
 * @since	1.0.0
 *
 * @param	void
 * @return	WP_Views
 */
function WP_Views() {
	global $WP_Views;
	
	// Instantiate only once.
	if( !isset($WP_Views) ) {
		$WP_Views = new WP_Views();
		$WP_Views->init();
	}

	$GLOBALS['WP_Views'] = $WP_Views;
	return $WP_Views;
}

/*
 * Hook WP_Views early onto the 'plugins_loaded' action.
 *
 * This gives all other plugins the chance to load before WP_Views,
 * to get their actions, filters, and overrides setup without
 * WP_Views being in the way.
 */
if ( defined( 'WP_VIEWS_LATE_LOAD' ) ) {
	add_action( 'plugins_loaded', 'WP_Views', (int) WP_VIEWS_LATE_LOAD );

// "And now here's something we hope you'll really like!"
} else {
	WP_Views();
}

endif; // class_exists check
