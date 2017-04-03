<?php
/*
* Plugin Name: Simplistic SEO
* Plugin URI: http://walkeezy.ch
* Description: All SEO you will ever need.
* Version: 1.0.0
* Author: Kevin Walker
* Author URI: http://walkeezy.ch
* License: GPL2
*/

add_action('wp_ajax_generate_title', 'ajax_generateTitle');

function ajax_generateTitle() {

	echo generateTitle($_POST['string'], $_POST['pageid']);

	exit();
}

function generateTitle($title, $pageid = NULL) {

	$variables = array(
		'sitetitle' => get_bloginfo('title'),
		'sitedesc' => get_bloginfo('description'),
		'pagetitle' => get_the_title($pageid)
	);

	foreach($variables as $key => $value){
		$title = str_replace('{'.$key.'}', $value, $title);
	}

	return $title;
}

// ADD METATAGS TO THE HEAD
//-----------------------------------------------------------------------

function sseo_metadescription() {
	global $post;
	if($post){
		// Get description from post meta
		$sseo_description = get_post_meta($post->ID, '_sseo_metadescription', true);
		// If empty, get default meta description
		if(empty($sseo_description)) {
			$sseo_description = esc_attr(get_option('sseo_default_metadescription'));
		} ?>
	<meta type="description" content="<?php echo $sseo_description; ?>" />
	<?php }
}

add_filter( 'wp_head', 'sseo_metadescription', 1 );

function sseo_title() {
	global $post;
	if($post){
		// Get title from post meta
		$sseo_title_string = get_post_meta($post->ID, '_sseo_title', true);
		// If empty, get default title pattern
		if(empty($sseo_title_string)) {
			$sseo_title_string = esc_attr(get_option('sseo_title_pattern'));
		}
		$sseo_title = generateTitle($sseo_title_string);
		return $sseo_title;
	}
}

add_filter('pre_get_document_title', 'sseo_title', 10, 1);


// ADD CSS TO THE ADMIN
//-----------------------------------------------------------------------

function sseo_admin_assets() {
	// CSS
	wp_register_style( 'sseo_admin_css', plugin_dir_url( __FILE__ ) . 'dist/styles.min.css', false, '1.9' );
	wp_enqueue_style( 'sseo_admin_css' );
	// JS
	wp_register_script( 'sseo_admin_js', plugin_dir_url( __FILE__ ) . 'dist/functions.min.js', false, '1.6' );
	wp_enqueue_script( 'sseo_admin_js' );
}

add_action( 'admin_enqueue_scripts', 'sseo_admin_assets' );


// SETTINGS PAGE
//-----------------------------------------------------------------------

function admin_menu() {
	add_options_page('SEO Einstellungen', 'SEO Einstellungen', 'manage_options', 'seo_settings', 'settings_page');
}

add_action( 'admin_menu', 'admin_menu' );

function settings_page() { ?>

	<div class="wrap">
		<h1>SEO Einstellungen</h1>
		<form method="post" action="options.php">
			<div class="sseo-settings-wrapper">
				<div class="sseo-settings-left">
					<?php settings_fields( 'sseo_settings' ); ?>
					<?php do_settings_sections( 'sseo_settings' ); ?>
					<h2>Title</h2>
					<div class="sseo-settings-input-wrapper">
						<p><span class="sseo-settings-input-label">Nach diesem Muster wird der Titel generiert, wenn für eine Seite kein spezifischer Titel angeben ist.</span></p>
						<input type="text" name="sseo_title_pattern" class="regular-text" id="sseo_title_pattern" value="<?php echo esc_attr(get_option('sseo_title_pattern')); ?>" />
						<div class="sseo-settings-input-placeholders"><p>Platzhalter: <a class="sseo-input-placeholder" data-placeholder="{sitetitle}" data-target="sseo_title_pattern">Website-Titel</a><a class="sseo-input-placeholder" data-placeholder="{sitedesc}" data-target="sseo_title_pattern">Website-Beschreibung</a><a class="sseo-input-placeholder" data-placeholder="{pagetitle}" data-target="sseo_title_pattern">Seitentitel</a></p></div>
					</div>
					<h2>Metadescription</h2>
					<div class="sseo-settings-input-wrapper">
						<p><span class="sseo-settings-input-label">Diese Metadescription wird immer dann angezeigt, wenn für eine Seite keine spezifische Metadescription angegeben ist.</span><span id="sseo_default_metadescription_info" class="length-info"></span></p>
						<textarea name="sseo_default_metadescription" class="large-text" id="sseo_default_metadescription"><?php echo esc_attr(get_option('sseo_default_metadescription')); ?></textarea>
					</div>
				</div>
				<div class="sseo-settings-right"></div>
				<div class="clear"></div>
				<?php submit_button(); ?>
			</div>
		</form>
	</div>

<?php }

function register_settings() {
	register_setting( 'sseo_settings', 'sseo_title_pattern' );
	register_setting( 'sseo_settings', 'sseo_default_metadescription' );
}

add_action( 'admin_init', 'register_settings' );


// METABOX
//-----------------------------------------------------------------------

function register_metabox() {
	add_meta_box( 'sseo-metabox', 'SEO Einstellungen', 'render_metabox', 'page' );
}

add_action( 'add_meta_boxes', 'register_metabox' );

function render_metabox() {

	global $post;
  $values = get_post_custom( $post->ID );
	$sseo_title = isset( $values['_sseo_title'] ) ? $values['_sseo_title'][0] : '';
	$sseo_title_default_string = esc_attr(get_option('sseo_title_pattern'));
	$sseo_title_default = generateTitle($sseo_title_default_string);
	$sseo_metadescription = isset( $values['_sseo_metadescription'] ) ? $values['_sseo_metadescription'][0] : '';
	$sseo_metadescription_default = esc_attr(get_option('sseo_default_metadescription'));

	wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' ); ?>

	<div id="sseo-meta-editor">
		<p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="sseo-title">Title</label><span id="sseo-title-info" class="length-info"></span></p>
		<input type="text" name="sseo-title" id="sseo-title" value="<?php echo $sseo_title; ?>" />
		<input type="hidden" name="sseo-pageid" id="sseo-pageid" value="<?php echo $_GET['post']; ?>" />
		<input type="hidden" name="sseo-title-default" id="sseo-title-default" value="<?php echo $sseo_title_default; ?>" />
		<p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="sseo-metadescription">Metadescription</label><span id="sseo-metadescription-info" class="length-info"></span></p>
		<textarea name="sseo-metadescription" class="postbox" id="sseo-metadescription"><?php echo $sseo_metadescription; ?></textarea>
		<input type="hidden" name="sseo-metadescription-default" id="sseo-metadescription-default" value="<?php echo $sseo_metadescription_default; ?>" />
	</div>
	<div id="sseo-preview">
		<p class="post-attributes-label-wrapper post-attributes-label">Vorschau</p>
		<div id="sseo-google-preview-wrapper">
			<span id="sseo-preview-title"><?php if(!empty($sseo_title)): echo generateTitle($sseo_title); else: echo $sseo_title_default; endif; ?></span>
			<span id="sseo-preview-url"><?php the_permalink(); ?><span id="sseo-preview-url-arrow"></span></span>
			<span id="sseo-preview-metadescription"><?php if(!empty($sseo_metadescription)): echo $sseo_metadescription; else: echo $sseo_metadescription_default; endif; ?></span>
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

add_action( 'save_post', 'save_metabox' );

?>
