<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WP_Views_List_Table extends WP_List_Table {

	/** @var count found items */
	private static $found_items = 0;

	public static function define_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'Title', 'wp_views' ),
			'shortcode' => __( 'Shortcode', 'wp_views' ),
			'author' => __( 'Author', 'wp_views' ),
			'date' => __( 'Date', 'wp_views' ),
		);

		return $columns;
	}

	public function __construct() {
		parent::__construct( array(
			'singular' => 'post',
			'plural' => 'posts',
			'ajax' => false,
		) );
	}

	/**
	 * list
	 *
	 * Get list of all views
	 *
	 * @since	1.0.0
	 *
	 * @param	array $args, filter args
	 * @return	void
	 */	
	function get_items( $args = '' ) {
		// WP Query default args
		$defaults = array(
			'post_status' => 'any',
			'posts_per_page' => -1,
			'offset' => 0,
			'orderby' => 'ID',
			'order' => 'ASC',
		);

		// Parse args
		$args = wp_parse_args( $args, $defaults );
		$args['post_type'] = WP_VIEWS_POST_TYPE;

		$q = new WP_Query();
		$posts = $q->query( $args );

		self::$found_items = $q->found_posts;

		$objs = array();

		foreach ( (array) $posts as $post ) {
			$objs[] = new WP_Views_View( $post );
		}

		return $objs;
	}

	public function prepare_items() {
		$current_screen = get_current_screen();
		$per_page = $this->get_items_per_page( 'wp_views_views_per_page' );

		$args = array(
			'posts_per_page' => $per_page,
			'orderby' => 'title',
			'order' => 'ASC',
			'offset' => ( $this->get_pagenum() - 1 ) * $per_page,
		);

		if ( ! empty( $_REQUEST['s'] ) ) {
			$args['s'] = $_REQUEST['s'];
		}

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			if ( 'title' == $_REQUEST['orderby'] ) {
				$args['orderby'] = 'title';
			} elseif ( 'author' == $_REQUEST['orderby'] ) {
				$args['orderby'] = 'author';
			} elseif ( 'date' == $_REQUEST['orderby'] ) {
				$args['orderby'] = 'date';
			}
		}

		if ( ! empty( $_REQUEST['order'] ) ) {
			if ( 'asc' == strtolower( $_REQUEST['order'] ) ) {
				$args['order'] = 'ASC';
			} elseif ( 'desc' == strtolower( $_REQUEST['order'] ) ) {
				$args['order'] = 'DESC';
			}
		}

		$this->items = $this->get_items( $args );

		$total_items = self::$found_items;
		$total_pages = ceil( $total_items / $per_page );

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'total_pages' => $total_pages,
			'per_page' => $per_page,
		) );
	}

	public function get_columns() {
		return get_column_headers( get_current_screen() );
	}

	protected function get_sortable_columns() {
		$columns = array(
			'title' => array( 'title', true ),
			'author' => array( 'author', false ),
			'date' => array( 'date', false ),
		);

		return $columns;
	}

	protected function get_bulk_actions() {
		$actions = array(
			'delete' => __( 'Delete', 'wp_views' ),
		);

		return $actions;
	}

	protected function column_default( $item, $column_name ) {
		return '';
	}

	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			$this->_args['singular'],
			$item->id()
		);
	}

	public function column_title( $item ) {
		$edit_link = add_query_arg(
			array(
				'post' => absint( $item->id() ),
				'action' => 'edit',
			),
			menu_page_url( 'wp-views', false )
		);

		$output = sprintf(
			'<a class="row-title" href="%1$s" aria-label="%2$s">%3$s</a>',
			esc_url( $edit_link ),
			esc_attr( sprintf(
				/* translators: %s: title of contact form */
				__( 'Edit &#8220;%s&#8221;', 'wp_views' ),
				$item->title()
			) ),
			esc_html( $item->title() )
		);

		$output = sprintf( '<strong>%s</strong>', $output );

		return $output;
	}

	protected function handle_row_actions( $item, $column_name, $primary ) {
		if ( $column_name !== $primary ) {
			return '';
		}

		$edit_link = add_query_arg(
			array(
				'post' => absint( $item->id() ),
				'action' => 'edit',
			),
			menu_page_url( 'wp-views', false )
		);

		$actions = array(
			'edit' => WP_Views_Utility::get_link( $edit_link, __( 'Edit', 'wp_views' ) ),
		);

		if ( current_user_can( 'wp_views_edit_contact_form', $item->id() ) ) {
			$copy_link = add_query_arg(
				array(
					'post' => absint( $item->id() ),
					'action' => 'copy',
				),
				menu_page_url( 'wp-views', false )
			);

			$copy_link = wp_nonce_url(
				$copy_link,
				'wp-views-copy-view_' . absint( $item->id() )
			);

			$actions = array_merge( $actions, array(
				'copy' => WP_Views_Utility::get_link( $copy_link, __( 'Duplicate', 'wp_views' ) ),
			) );
		}

		return $this->row_actions( $actions );
	}

	public function column_author( $item ) {
		$post = get_post( $item->id() );

		if ( ! $post ) {
			return;
		}

		$author = get_userdata( $post->post_author );

		if ( false === $author ) {
			return;
		}

		return esc_html( $author->display_name );
	}

	public function column_shortcode( $item ) {
		$shortcodes = array( $item->get_shortcode() );

		$output = '';

		foreach ( $shortcodes as $shortcode ) {
			$output .= "\n" . '<span class="shortcode"><input type="text"'
				. ' onfocus="this.select();" readonly="readonly"'
				. ' value="' . esc_attr( $shortcode ) . '"'
				. ' class="large-text code" /></span>';
		}

		return trim( $output );
	}

	public function column_date( $item ) {
		$post = $item->post();

		if ( ! $post ) {
			return;
		}

		$t_time = mysql2date( __( 'Y/m/d g:i:s A', 'wp_views' ),
			$post->post_date, true );
		$m_time = $post->post_date;
		$time = mysql2date( 'G', $post->post_date )
			- get_option( 'gmt_offset' ) * 3600;

		$time_diff = time() - $time;

		if ( $time_diff > 0 and $time_diff < 24*60*60 ) {
			$h_time = sprintf(
				/* translators: %s: time since the creation of the contact form */
				__( '%s ago', 'wp_views' ),
				human_time_diff( $time )
			);
		} else {
			$h_time = mysql2date( __( 'Y/m/d', 'wp_views' ), $m_time );
		}

		return sprintf( '<abbr title="%2$s">%1$s</abbr>',
			esc_html( $h_time ),
			esc_attr( $t_time )
		);
	}
}
