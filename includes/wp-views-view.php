<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WP_Views_View
 *
 * Views Singleton Class
 *
 * @since	1.0.0
 *
 * @param	void
 * @return	void
 */	

if( ! class_exists('WP_Views_View') ) :

class WP_Views_View {
	/** @var wp post of view */
	var $post = null;

	/** @var wp post of view */
	private $id;
	private $name;
	private $title;
	private $locale;
	private $properties = array();
	private $unit_tag;
	private $responses_count = 0;
	private $scanned_form_tags;
	private $shortcode_atts = array();

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
	function __construct( $post = null ) {
		$this->init( $post );
	}

	/**
	 * post
	 *
	 * View Wp Post
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	post object
	 */	
	function post() {
		return $this->post;
	}

	/**
	 * id()
	 *
	 * View ID
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	view id
	 */	
	function id() {
		return $this->id ? $this->id : -1;
	}

	/**
	 * title()
	 *
	 * View title
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	view title
	 */	
	function title() {
		return $this->title ? $this->title : 'Untitled';
	}

	public function locale() {
		return $this->locale;
	}

	/**
	 * initial()
	 *
	 * Check if is initial view
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	boolean
	 */	
	public function initial() {
		return empty( $this->id );
	}

	/**
	 * init()
	 *
	 * Init WP Views View
	 *
	 * @since	1.0.0
	 *
	 * @param	$post
	 * @return	void
	 */	
	function init( $post ) {
		$post = get_post( $post );

		if ( $post && WP_VIEWS_POST_TYPE == get_post_type( $post ) ) {
			$this->id = $post->ID;
			$this->name = $post->post_name;
			$this->title = $post->post_title;
			$this->locale = get_post_meta( $post->ID, '_locale', true );

			$properties = $this->get_properties();

			foreach ( $properties as $key => $value ) {
				if ( metadata_exists( 'post', $post->ID, '_' . $key ) ) {
					$properties[$key] = get_post_meta( $post->ID, '_' . $key, true );
				} elseif ( metadata_exists( 'post', $post->ID, $key ) ) {
					$properties[$key] = get_post_meta( $post->ID, $key, true );
				}
			}

			$this->properties = $properties;
			$this->post = $post;
		}

		do_action( 'wp_views_view_init', $this );
		return $this;
	}

	public function prop( $name ) {
		$props = $this->get_properties();
		return isset( $props[$name] ) ? $props[$name] : null;
	}

	public function get_properties() {
		$properties = (array) $this->properties;

		$properties = wp_parse_args( $properties, array(
			'additional_settings' => '',
		) );

		$properties = (array) apply_filters( 'wp_views_view_properties',
			$properties, $this );

		return $properties;
	}

	public function set_properties( $properties ) {
		$defaults = $this->get_properties();

		$properties = wp_parse_args( $properties, $defaults );
		$properties = array_intersect_key( $properties, $defaults );

		$this->properties = $properties;
	}

	/**
	 * get_shortcode
	 *
	 * View get shortcode
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	public function get_shortcode( $args = '' ) {
		$title = str_replace( array( '"', '[', ']' ), '', $this->title() );

		$shortcode = sprintf( '[wp-views-view id="%1$d" title="%2$s"]',
				$this->id(), $title );

		return apply_filters( 'wp_views_shortcode',
			$shortcode, $args, $this );
	}

}

endif; // class_exists check
