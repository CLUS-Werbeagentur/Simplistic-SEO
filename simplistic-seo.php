<?php
/**
 * Plugin Name: Simplistic SEO
 * Plugin URI: http://walkeezy.ch
 * Description: All SEO you will ever need.
 * Version: 1.0.0
 * Author: Kevin Walker
 * Author URI: http://walkeezy.ch
 * License: GPL2
 */

 // ADD METATAGS TO THE HEAD
 //-----------------------------------------------------------------------

function sseo_metadescription() {

	global $post;
	$sseo_description = get_post_meta($post->ID, '_sseo_metadescription', true); ?>
<meta type="description" content="<?php echo $sseo_description; ?>" />

<?php }

add_filter( 'wp_head', 'sseo_metadescription', 1 );

function sseo_title() {

	global $post;
	$sseo_title = get_post_meta($post->ID, '_sseo_title', true);

	return $sseo_title;
}

add_filter('pre_get_document_title', 'sseo_title', 10, 1);


// ADD CSS TO THE ADMIN
//-----------------------------------------------------------------------

function sseo_admin_assets() {

	// CSS
	wp_register_style( 'sseo_admin_css', plugin_dir_url( __FILE__ ) . 'dist/styles.min.css', false, '1.0.2' );
	wp_enqueue_style( 'sseo_admin_css' );

	// JS
	wp_register_script( 'sseo_admin_js', plugin_dir_url( __FILE__ ) . 'dist/functions.min.js', false, '1.0.2' );
	wp_enqueue_script( 'sseo_admin_js' );
}

add_action( 'admin_enqueue_scripts', 'sseo_admin_assets' );


// SETTINGS PAGE
//-----------------------------------------------------------------------

class settingsPage {

	function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	function admin_menu() {
		add_options_page(
			'Page Title',
			'SEO Einstellungen',
			'manage_options',
			'options_page_slug',
			array(
				$this,
				'settings_page'
			)
		);
	}

	function register_settings() {
		register_setting( 'sseo_settings', 'sseo_test' );
	}

	function settings_page() { ?>

		<div class="wrap">
			<h1><?php _e( 'SEO Einstellungen', 'simplistic-seo' ) ?></h1>

			<form method="post" action="options.php">
				<?php settings_fields( 'sseo_settings' ); ?>
				<?php do_settings_sections( 'sseo_settings' ); ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">Test</th>
						<td><input type="text" name="sseo_test" value="<?php echo esc_attr( get_option('sseo_test') ); ?>" /></td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>

	<?php }
}

new settingsPage;

// METABOX
//-----------------------------------------------------------------------

class metaBox {

	function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );
		add_action( 'save_post', array( $this, 'save_metabox' ) );
	}

	function register_metabox() {
		add_meta_box( 'sseo-metabox', __( 'SEO Einstellungen', 'simplistic-seo' ), array( $this, 'render_metabox' ), 'page' );
	}

	function render_metabox() {

		global $post;
    $values = get_post_custom( $post->ID );
		$sseo_title = isset( $values['_sseo_title'] ) ? $values['_sseo_title'][0] : '';
		$sseo_title_default = get_the_title()." – ".get_bloginfo('title');
		$sseo_metadescription = isset( $values['_sseo_metadescription'] ) ? $values['_sseo_metadescription'][0] : '';

		wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' ); ?>

		<div id="sseo-meta-editor">
			<p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="sseo-title">Title</label><span id="sseo-title-character-info"></span></p>
			<input type="text" name="sseo-title" id="sseo-title" value="<?php echo $sseo_title; ?>" />
			<input type="hidden" name="sseo-title-default" id="sseo-title-default" value="<?php echo $sseo_title_default; ?>" />
			<p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="sseo-metadescription">Metadescription</label><span id="sseo-metadescription-character-info"></span></p>
			<textarea name="sseo-metadescription" class="postbox" id="sseo-metadescription"><?php echo $sseo_metadescription; ?></textarea>
		</div>
		<div id="sseo-preview">
			<p class="post-attributes-label-wrapper post-attributes-label">Vorschau</p>
			<div id="sseo-google-preview-wrapper">
				<span id="sseo-preview-title"><?php if(!empty($sseo_title)): echo $sseo_title; else: echo $sseo_title_default; endif; ?></span>
				<span id="sseo-preview-url"><?php the_permalink(); ?><span id="sseo-preview-url-arrow"></span></span>
				<span id="sseo-preview-metadescription"><?php echo $sseo_metadescription; ?></span>
			</div>
		</div>
		<div class="clear"></div>

	<?php }

	function save_metabox($post_id) {

		// Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    // if our nonce isn't there, or we can't verify it, bail
    if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' ) ) return;

    // if our current user can't edit this post, bail
    if( !current_user_can( 'edit_post' ) ) return;

		if( isset( $_POST['sseo-title'] ) )
			update_post_meta( $post_id, '_sseo_title', esc_attr( $_POST['sseo-title'] ) );

    if( isset( $_POST['sseo-metadescription'] ) )
			update_post_meta( $post_id, '_sseo_metadescription', esc_attr( $_POST['sseo-metadescription'] ) );

	}

}

new metaBox;

?>
