<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('WP_Views_Admin') ) :

class WP_Views_Admin extends WP_Views_Base {

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
		// Include Admin Utility Class
		include_once( WP_VIEWS_PATH . 'admin/wp-views-admin-utility.php');

		// Admin menu
		add_action( 'admin_menu', array( $this, 'admin_menu' )  );

		// Show post metaboxes only if plugin is enabled
		if( $this->get_setting( 'plugin_enabled', true ) ) {
			// Actions
			add_action( 'add_meta_boxes', array( $this, 'register_metaboxes' ) );
			add_action( 'save_post', array( $this, 'save_metaboxes' ) );
		}

		// Enqueue scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Setup Ajax action hook
		add_action( 'wp_ajax_wp_views_ajax', array( $this, 'ajax_load_urls' ) );
	}

	/**
	 * ajax_load_urls
	 *
	 * ajax_load_urls callback
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function ajax_load_urls() {
		// Check ajax nonce for security
		check_ajax_referer( 'wp-views-ajax-nonce', 'security' );

		wp_send_json([
			'status' => 'success',
			'options' => []
		]);
		exit();
	}

	/**
	 * enqueue_scripts
	 *
	 * admin_enqueue_scripts callback
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function enqueue_scripts() {
		// Vue JS APP & Components
		wp_enqueue_script( 'wp-views-admin-vuejs', WP_VIEWS_URL . 'admin/assets/js/vue.js', array(), WP_VIEWS_VERSION, true );
		wp_enqueue_script( 'wp-views-admin-vuejs-components', WP_VIEWS_URL . 'admin/assets/js/vue-components.js', array(), WP_VIEWS_VERSION, true );
		wp_enqueue_script( 'wp-views-admin-vuejs-app', WP_VIEWS_URL . 'admin/assets/js/vue-app.js', array(), WP_VIEWS_VERSION, true );
		
		// Admin Scripts
		wp_enqueue_style( 'wp-views-admin-style', WP_VIEWS_URL . 'admin/assets/css/admin.css', array(), WP_VIEWS_VERSION );
		wp_register_script( 'wp-views-admin-script', WP_VIEWS_URL . 'admin/assets/js/admin.js', array(), WP_VIEWS_VERSION, true );
		wp_localize_script( 'wp-views-admin-script', 'wp_views_settings', array( 'ajax_url' => admin_url('admin-ajax.php'), 'wp_views_ajax_nonce' => wp_create_nonce('wp-views-ajax-nonce')) );

		// Enqueue scripts
		wp_enqueue_script( 'wp-views-admin-script' );
	}
	
	/**
	 * admin_menu
	 *
	 * admin_menu callback
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function admin_menu() {
		global $_wp_last_object_menu;
		$_wp_last_object_menu++;

		$slug = 'wp-views';

		// WP Views Admin Page
		add_menu_page( __( 'WP Views', $slug ),
			__( 'WP Views', 'wp-views' ),
			'wp_views_read_view', 'wp-views',
			array( $this, 'admin_management_page' ), 'dashicons-welcome-view-site',
			$_wp_last_object_menu );

		// WP Views Admin Edit Page
		$edit = add_submenu_page( $slug,
			__( 'Edit WP View', 'wp-views' ),
			__( 'WP Views', 'wp-views' ),
			'wp_views_read_view', 'wp-views',
			array( $this, 'admin_management_page' ) );

		add_action( 'load-' . $edit, array( $this, 'wp_views_load_admin' ), 10, 0 );

		// WP Views Admin Add Page
		$addnew = add_submenu_page( $slug,
			__( 'Add New View', 'wp-views' ),
			__( 'Add New', 'wp-views' ),
			'wp_views_edit_views', 'wp-views-new',
			array( $this, 'admin_add_new_page' ) );

		add_action( 'load-' . $addnew, array( $this, 'wp_views_load_admin' ), 10, 0 );

		// WP Views Settings Add Page
		$settings = add_submenu_page( $slug,
			__( 'Settings', 'wp-views' ),
			__( 'Setting', 'wp-views' ),
			'wp_views_manage_settings', 'wp-views-settings',
			array( $this, 'wp_views_admin_settings_page' ) );

		add_action( 'load-' . $settings, array( $this, 'wp_views_load_settings_page' ), 10, 0 );
	}

	function wp_views_load_admin() {
		global $plugin_page;

		$action = WP_Views_Utility::current_action();

		do_action( 'wp_views_admin_load',
			isset( $_GET['page'] ) ? trim( $_GET['page'] ) : '',
			$action
		);

		if ( 'save' == $action ) {

		}

		if ( 'copy' == $action ) {

		}

		if ( 'delete' == $action ) {
			
		}

		$post = null;

		$current_screen = get_current_screen();

		// Include listing table
		if ( ! class_exists( 'WP_Views_List_Table' ) ) {
			include_once( WP_VIEWS_PATH . 'admin/wp-views-list-table.php');
		}

		add_filter( 'manage_' . $current_screen->id . '_columns',
			array( 'WP_Views_List_Table', 'define_columns' ), 10, 0 );

		add_screen_option( 'per_page', array(
			'default' => 20,
			'option' => 'wp_views_views_per_page',
		) );
	}

	function admin_add_new_page() {
		$post = $this->load_view( null );
		$post_id = -1;

		// Include Editor Files
		include_once( WP_VIEWS_PATH . 'admin/wp-views-editor.php');
		include_once( WP_VIEWS_PATH . 'admin/wp-views-edit-form.php');
	}

	function admin_management_page() {
		$list_table = new WP_Views_List_Table();
		$list_table->prepare_items();

		?>
		<div class="wrap" id="wp_views-list-table">

		<h1 class="wp-heading-inline"><?php
			echo esc_html( __( 'WP Views', 'wp-views' ) );
		?></h1>

		<?php
			if ( current_user_can( 'wp_views_edit_views' ) ) {
				echo WP_Views_Utility::get_link(
					menu_page_url( 'wp-views-new', false ),
					__( 'Add New', 'wp-views' ),
					array( 'class' => 'page-title-action' )
				);
			}

			if ( ! empty( $_REQUEST['s'] ) ) {
				echo sprintf( '<span class="subtitle">'
					/* translators: %s: search keywords */
					. __( 'Search results for &#8220;%s&#8221;', 'wp-views' )
					. '</span>', esc_html( $_REQUEST['s'] )
				);
			}
		?>

		<hr class="wp-header-end">

		<?php
			do_action( 'wp_views_admin_warnings',
				'wp_views', WP_Views_Utility::current_action(), null );

			do_action( 'wp_views_admin_notices',
				'wp_views', WP_Views_Utility::current_action(), null );
		?>

		<form method="get" action="">
			<input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
			<?php $list_table->search_box( __( 'Search WP Views', 'wp-views' ), 'wp_views-contact' ); ?>
			<?php $list_table->display(); ?>
		</form>

		</div>
		<?php
	}
		
	/**
	 * admin_menu
	 *
	 * admin_menu callback
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function wp_views_admin_settings_page() {
		// Check for permission
		if ( ! current_user_can( 'wp_views_manage_settings' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		// Save submitted form
	    $messages = $this->settings_page_save();

		// Include admin settings page
	    include_once( WP_VIEWS_PATH . 'admin/templates/settings.php');
	}


	/**
	 * admin_menu_settings_page_save
	 *
	 * Save admin settings
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function wp_views_load_settings_page() {
		// Check for permission
		if ( !current_user_can( 'wp_views_manage_settings' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		if ( ! isset( $_POST['wp_views_settings'] ) ) {
	        return;
	    }

	    $plugin_enabled = isset($_POST['plugin_enabled']) ? $_POST['plugin_enabled'] : 0;
	    $show_in_excerpt = isset($_POST['show_in_excerpt']) ? $_POST['show_in_excerpt'] : 0;

	    // Create option array to save
	    $options = [
	    	'plugin_enabled' => $plugin_enabled,
	    	'show_in_excerpt' => $show_in_excerpt
	    ];

	    // Update options
	    update_option( 'wp_views_settings',  $options );

	    // Update Settings
	    $this->set_settings( $this->settings, $options );

	    return [
	    	'status' => 'success',
	    	'message' => __( 'Settings saved' )
	    ];
	}

	/**
	 * admin_menu_settings_page_save
	 *
	 * Save admin settings
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function settings_page_save() {
		// Check for permission
		if ( !current_user_can( 'wp_views_manage_settings' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		if ( ! isset( $_POST['wp_views_settings'] ) ) {
	        return;
	    }

	    $plugin_enabled = isset($_POST['plugin_enabled']) ? $_POST['plugin_enabled'] : 0;
	    $show_in_excerpt = isset($_POST['show_in_excerpt']) ? $_POST['show_in_excerpt'] : 0;

	    // Create option array to save
	    $options = [
	    	'plugin_enabled' => $plugin_enabled,
	    	'show_in_excerpt' => $show_in_excerpt
	    ];

	    // Update options
	    update_option( 'wp_views_settings',  $options );

	    // Update Settings
	    $this->set_settings( $this->settings, $options );

	    return [
	    	'status' => 'success',
	    	'message' => __( 'Settings saved' )
	    ];
	}

	/**
	 * register_metaboxes
	 *
	 * add_meta_boxes callback
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function register_metaboxes() {
		add_meta_box(
	        'wp_views',
	        __( 'Seo Keyword Internal Link', 'wp_views' ),
	        array( $this, 'wp_views_meta_box_callback' )
	    );
	}
	
	/**
	 * wp_views_meta_box_callback
	 *
	 * add_meta_box callback
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function wp_views_meta_box_callback( $post ) {
		// Add a nonce field so we can check for it later.
	    wp_nonce_field( 'wp_views_nonce', 'wp_views_nonce' );

	    // Get meta values
	    $keyword = get_post_meta( $post->ID, 'wp_views_keyword', true );
	    $url = get_post_meta( $post->ID, 'wp_views_url', true );

	    // Get all post types
		$post_types = get_post_types([
			'public'   => true,
  			'_builtin' => false
		], 'names', 'and');

		// builtin post types
		$post_types['post'] = 'post';
		$post_types['page'] = 'page';

		// build options
		$post_types_options = [];
		foreach ($post_types as $slug => $name) {
			$post_types_options[ 'post_type:' . $slug ] = $name;
		}

		// Get all taxonomies
		$taxonomies = get_taxonomies([
			'public'   => true,
  			'_builtin' => false
		], 'names', 'and');
		
		// builtin taxonomies
		$taxonomies['category'] = 'category';
		$taxonomies['post_tag'] = 'post_tag';

		// Exclude taxonomies
		$exclude_taxonomies = array( 'product_shipping_class' );

		// build options
		$taxonomies_options = [];
		foreach ($taxonomies as $slug => $name) {
			if( in_array( $slug, $exclude_taxonomies ) ) {
				continue;
			}

			$taxonomies_options[ 'taxonomy:' . $slug ] = $name;
		}

		// Object types
		$object_types = [
			'Post Types' => $post_types_options,
			'Taxonomies' => $taxonomies_options,
			'Custom Link' => [
				'custom_link' => 'Custom Link'
			],
		];

		// Filter object types
		$object_types = apply_filters('wp_views_object_types', $object_types);

		// get saved meta
		$saved_meta = get_post_meta( $post->ID, 'wp_views_meta', true );

	    // Include metaboxes fields
	    include_once( WP_VIEWS_PATH . 'admin/templates/meta-boxes.php');
	}
	
	/**
	 * save_metaboxes
	 *
	 * save_post callback
	 *
	 * @since	1.0.0
	 *
	 * @param	void
	 * @return	void
	 */	
	function save_metaboxes( $post_id ) {
		// Check if our nonce is set.
	    if ( ! isset( $_POST['wp_views_nonce'] ) ) {
	        return;
	    }

	    // Verify that the nonce is valid.
	    if ( ! wp_verify_nonce( $_POST['wp_views_nonce'], 'wp_views_nonce' ) ) {
	        return;
	    }

	    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
	    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	        return;
	    }

	    // Check the user's permissions.
	    if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

	        if ( ! current_user_can( 'edit_page', $post_id ) ) {
	            return;
	        }

	    }
	    else {

	        if ( ! current_user_can( 'edit_post', $post_id ) ) {
	            return;
	        }
	    }

	    /* OK, it's safe for us to save the data now. */

	    // Make sure that it is set.
	    if ( ! isset( $_POST['wp_views_keyword'] ) ) {
	        return;
	    }

	    if ( ! isset( $_POST['wp_views_object_type'] ) ) {
	        return;
	    }

	    if ( ! isset( $_POST['wp_views_object_id'] ) ) {
	        return;
	    }

	    if ( ! isset( $_POST['wp_views_custom_url'] ) ) {
	        return;
	    }

	    // Sanitize user input.
	    $keyword = sanitize_text_field( $_POST['wp_views_keyword'] );
	    $object_type = sanitize_text_field( $_POST['wp_views_object_type'] );
	    $object_id = sanitize_text_field( $_POST['wp_views_object_id'] );
	    $custom_url = esc_url( $_POST['wp_views_custom_url'] );

	    $post_meta = [
	    	'keyword' => $keyword,
	    	'object_type' => $object_type,
	    	'object_id' => $object_id,
	    	'custom_url' => $custom_url,
	    ];

	    // Update the meta field in the database.
	    update_post_meta( $post_id, 'wp_views_meta', $post_meta );
	}
}

endif; // class_exists check
