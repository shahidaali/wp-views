<?php

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>
<div class="wrap" id="wp-views-view-editor">

<h1 class="wp-heading-inline"><?php
	if ( $post->initial() ) {
		echo esc_html( __( 'Add New View', 'wp-views' ) );
	} else {
		echo esc_html( __( 'Edit View', 'wp-views' ) );
	}
?></h1>

<?php
	if ( ! $post->initial() and current_user_can( 'wp_views_edit_views' ) ) {
		echo WP_Views_Utility::get_link(
			menu_page_url( 'wp-views-new', false ),
			__( 'Add New', 'wp-views' ),
			array( 'class' => 'page-title-action' )
		);
	}
?>

<hr class="wp-header-end">

<?php
	do_action( 'wp_views_admin_warnings',
		$post->initial() ? 'wp-views-new' : 'wp-views',
		WP_Views_Utility::current_action(),
		$post
	);

	do_action( 'wp_views_admin_notices',
		$post->initial() ? 'wp-views-new' : 'wp-views',
		WP_Views_Utility::current_action(),
		$post
	);
?>

<?php
if ( $post ) :

	if ( current_user_can( 'wp_views_edit_view', $post_id ) ) {
		$disabled = '';
	} else {
		$disabled = ' disabled="disabled"';
	}
?>

<form method="post" action="<?php echo esc_url( add_query_arg( array( 'post' => $post_id ), menu_page_url( 'wp-views', false ) ) ); ?>" id="wp-views-admin-form-element"<?php do_action( 'wp-views_post_edit_form_tag' ); ?>>
<?php
	if ( current_user_can( 'wp_views_edit_view', $post_id ) ) {
		wp_nonce_field( 'wp-views-save-view_' . $post_id );
	}
?>
<input type="hidden" id="post_ID" name="post_ID" value="<?php echo (int) $post_id; ?>" />
<input type="hidden" id="wp-views-locale" name="wp-views-locale" value="<?php echo esc_attr( $post->locale() ); ?>" />
<input type="hidden" id="hiddenaction" name="action" value="save" />
<input type="hidden" id="active-tab" name="active-tab" value="<?php echo isset( $_GET['active-tab'] ) ? (int) $_GET['active-tab'] : '0'; ?>" />

<div id="poststuff">
<div id="post-body" class="metabox-holder columns-2">
<div id="post-body-content">
<div id="titlediv">
<div id="titlewrap">
	<label class="screen-reader-text" id="title-prompt-text" for="title"><?php echo esc_html( __( 'Enter title here', 'wp-views' ) ); ?></label>
<?php
	$posttitle_atts = array(
		'type' => 'text',
		'name' => 'post_title',
		'size' => 30,
		'value' => $post->initial() ? '' : $post->title(),
		'id' => 'title',
		'spellcheck' => 'true',
		'autocomplete' => 'off',
		'disabled' =>
			current_user_can( 'wp_views_edit_view', $post_id ) ? '' : 'disabled',
	);

	echo sprintf( '<input %s />', WP_Views_Utility::format_atts( $posttitle_atts ) );
?>
</div><!-- #titlewrap -->

<div class="inside">
<?php
	if ( ! $post->initial() ) :
	?>
	<p class="description">
	<label for="wp-views-shortcode"><?php echo esc_html( __( "Copy this shortcode and paste it into your post, page, or text widget content:", 'wp-views' ) ); ?></label>
	<span class="shortcode wp-ui-highlight"><input type="text" id="wp-views-shortcode" onfocus="this.select();" readonly="readonly" class="large-text code" value="<?php echo esc_attr( $post->get_shortcode() ); ?>" /></span>
	</p>
	<?php
	endif;
?>
</div>
</div><!-- #titlediv -->
</div><!-- #post-body-content -->

<div id="postbox-container-1" class="postbox-container">
<?php if ( current_user_can( 'wp_views_edit_view', $post_id ) ) : ?>
<div id="submitdiv" class="postbox">
<h3><?php echo esc_html( __( 'Status', 'wp-views' ) ); ?></h3>
<div class="inside">
<div class="submitbox" id="submitpost">

<div id="minor-publishing-actions">

<div class="hidden">
	<input type="submit" class="button-primary" name="wp-views-save" value="<?php echo esc_attr( __( 'Save', 'wp-views' ) ); ?>" />
</div>

<?php
	if ( ! $post->initial() ) :
		$copy_nonce = wp_create_nonce( 'wp-views-copy-view_' . $post_id );
?>
	<input type="submit" name="wp-views-copy" class="copy button" value="<?php echo esc_attr( __( 'Duplicate', 'wp-views' ) ); ?>" <?php echo "onclick=\"this.form._wpnonce.value = '$copy_nonce'; this.form.action.value = 'copy'; return true;\""; ?> />
<?php endif; ?>
</div><!-- #minor-publishing-actions -->

<div id="misc-publishing-actions">
<?php do_action( 'wp-views_admin_misc_pub_section', $post_id ); ?>
</div><!-- #misc-publishing-actions -->

<div id="major-publishing-actions">

<?php
if ( ! $post->initial() ) :
	$delete_nonce = wp_create_nonce( 'wp-views-delete-view_' . $post_id );
	?>
	<div id="delete-action">
		<input type="submit" name="wp-views-delete" class="delete submitdelete" value="<?php echo esc_attr( __( 'Delete', 'wp-views' ) ); ?>" <?php echo "onclick=\"if (confirm('" . esc_js( __( "You are about to delete this contact form.\n  'Cancel' to stop, 'OK' to delete.", 'wp-views' ) ) . "')) {this.form._wpnonce.value = '$delete_nonce'; this.form.action.value = 'delete'; return true;} return false;\""; ?> />
	</div><!-- #delete-action -->
<?php endif; ?>

<div id="publishing-action">
	<span class="spinner"></span>
	<?php WP_Views_Admin_Utility::admin_save_button( $post_id ); ?>
</div>
<div class="clear"></div>
</div><!-- #major-publishing-actions -->
</div><!-- #submitpost -->
</div>
</div><!-- #submitdiv -->
<?php endif; ?>

<div id="informationdiv" class="postbox">
<h3><?php echo esc_html( __( "Do you need help?", 'wp-views' ) ); ?></h3>
<div class="inside">
	<p><?php echo esc_html( __( "Here are some available options to help solve your problems.", 'wp-views' ) ); ?></p>
	<ol>
		<li><?php echo sprintf(
			/* translators: 1: FAQ, 2: Docs ("FAQ & Docs") */
			__( '%1$s &#38; %2$s', 'wp-views' ),
			WP_Views_Utility::get_link(
				__( 'https://contactform7.com/faq/', 'wp-views' ),
				__( 'FAQ', 'wp-views' )
			),
			WP_Views_Utility::get_link(
				__( 'https://contactform7.com/docs/', 'wp-views' ),
				__( 'Docs', 'wp-views' )
			)
		); ?></li>
		<li><?php echo WP_Views_Utility::get_link(
			__( 'https://wordpress.org/support/plugin/wp-views/', 'wp-views' ),
			__( 'Support Forums', 'wp-views' )
		); ?></li>
		<li><?php echo WP_Views_Utility::get_link(
			__( 'https://contactform7.com/custom-development/', 'wp-views' ),
			__( 'Professional Services', 'wp-views' )
		); ?></li>
	</ol>
</div>
</div><!-- #informationdiv -->

</div><!-- #postbox-container-1 -->

<div id="postbox-container-2" class="postbox-container">
	<?php if ( $post->initial() ) : ?>
		<div class="postbox">
			<div class="inside">
				<div class="wp__views__new_container">
					<div class="wp__views_new_field">
						<span class="label"><?php echo __( 'Show', 'wp-views' ); ?>:</span> 
						<select name="content_type">
							<?php foreach ($this->content_types as $key => $type) : ?>
								<?php $selected = ($key == 'post') ? 'selected="selected"' : ''; ?>
								<option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $type->label; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="wp__views_new_field">
						<span class="label"><?php echo __( 'of type', 'wp-views' ); ?>:</span> 
						<select name="post_type">
							<?php foreach (WP_Views_Admin_Utility::get_post_types() as $key => $post_type) : ?>
								<?php $selected = ($key == 'post') ? 'selected="selected"' : ''; ?>
								<option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $post_type->label; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="wp__views_new_field">
						<span class="label"><?php echo __( 'of type', 'wp-views' ); ?>:</span> 
						<select name="taxonomy">
							<?php foreach (WP_Views_Admin_Utility::get_taxonomies() as $key => $taxonomy) : ?>
								<?php $selected = ($key == 'post') ? 'selected="selected"' : ''; ?>
								<option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $taxonomy->label; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="wp__views_new_field">
						<span class="label"><?php echo __( 'sorted by', 'wp-views' ); ?>:</span> 
						<select name="sorted_by">
							
						</select>
					</div>
				</div>
			</div>
		</div>
	<?php else: ?>
		<div id="wp__views__app">
			<div class="wp__views_cols wp__views_cols_3 clearfix">
				<div class="wp__views_col wp__views_col_1">
					<!-- Display Title -->
					<wp-views-display-title
						panel-title="<?php echo esc_html( __( 'Title', 'wp-views' ) ); ?>"
						display-title="<?php echo esc_html( __( 'Content', 'wp-views' ) ); ?>"
					></wp-views-display-title>

					<!-- Display Format -->
					<wp-views-display-format
						panel-title="<?php echo esc_html( __( 'Format', 'wp-views' ) ); ?>"
						display-format="Table"
						setting-text="<?php echo esc_html( __( 'Settings', 'wp-views' ) ); ?>"
					></wp-views-display-format>
				</div>
			</div>
		</div><!-- #view-editor -->
	<?php endif; ?>

	<?php if ( current_user_can( 'wp_views_edit_view', $post_id ) ) : ?>
		<p class="submit"><?php WP_Views_Admin_Utility::admin_save_button( $post_id ); ?></p>
	<?php endif; ?>

</div><!-- #postbox-container-2 -->

</div><!-- #post-body -->
<br class="clear" />
</div><!-- #poststuff -->
</form>

<?php endif; ?>

</div><!-- .wrap -->
<?php
do_action( 'wp_views_admin_footer', $post );
