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

function sseo_metatags() {

	global $post;
	$description = get_post_meta($post->ID, '_sseo_metadescription', true); ?>

<meta type="description" content="<?php echo $description; ?>" />

<?php }

add_filter( 'wp_head', 'sseo_metatags', 1 );


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
		$metadescription = isset( $values['_sseo_metadescription'] ) ? $values['_sseo_metadescription'][0] : '';

		wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' ); ?>

		<p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="sseo-metadescription">Metadescription</label></p>
		<textarea name="metadescription" class="postbox" id="metadescription"><?php echo $metadescription; ?></textarea>

	<?php }

	function save_metabox($post_id) {

		// Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    // if our nonce isn't there, or we can't verify it, bail
    if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' ) ) return;

    // if our current user can't edit this post, bail
    if( !current_user_can( 'edit_post' ) ) return;

    if( isset( $_POST['metadescription'] ) )
			update_post_meta( $post_id, '_sseo_metadescription', esc_attr( $_POST['metadescription'] ) );

	}

}

new metaBox;

?>
