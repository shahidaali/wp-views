<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WP_Views_Utility
 *
 * Views Utitlity Class
 *
 * @since	1.0.0
 *
 * @param	void
 * @return	void
 */	

if( ! class_exists('WP_Views_Utility') ) :

class WP_Views_Utility {
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
	 * format_atts()
	 *
	 * Format atts
	 *
	 * @since	1.0.0
	 *
	 * @param	$atts to format
	 * @return	view id
	 */	
	public static function format_atts( $atts ) {
		$html = '';

		$prioritized_atts = array( 'type', 'name', 'value' );

		foreach ( $prioritized_atts as $att ) {
			if ( isset( $atts[$att] ) ) {
				$value = trim( $atts[$att] );
				$html .= sprintf( ' %s="%s"', $att, esc_attr( $value ) );
				unset( $atts[$att] );
			}
		}

		foreach ( $atts as $key => $value ) {
			$key = strtolower( trim( $key ) );

			if ( ! preg_match( '/^[a-z_:][a-z_:.0-9-]*$/', $key ) ) {
				continue;
			}

			$value = trim( $value );

			if ( '' !== $value ) {
				$html .= sprintf( ' %s="%s"', $key, esc_attr( $value ) );
			}
		}

		$html = trim( $html );

		return $html;
	}

	/**
	 * get_link()
	 *
	 * Get formated link
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	view id
	 */	
	public static function get_link( $url, $anchor_text, $args = '' ) {
		$defaults = array(
			'id' => '',
			'class' => '',
		);

		$args = wp_parse_args( $args, $defaults );
		$args = array_intersect_key( $args, $defaults );
		$atts = self::format_atts( $args );

		$link = sprintf( '<a href="%1$s"%3$s>%2$s</a>',
			esc_url( $url ),
			esc_html( $anchor_text ),
			$atts ? ( ' ' . $atts ) : '' );

		return $link;
	}

	/**
	 * current_action()
	 *
	 * Get current action to perform
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	action name
	 */	
	public static function current_action() {
		if ( isset( $_REQUEST['action'] ) and -1 != $_REQUEST['action'] ) {
			return $_REQUEST['action'];
		}

		if ( isset( $_REQUEST['action2'] ) and -1 != $_REQUEST['action2'] ) {
			return $_REQUEST['action2'];
		}

		return false;
	}
}

endif; // class_exists check
