<?php
/*
* Plugin Name: Simplistic SEO
* Description: Everything you need for basic SEO in one simple plugin.
* Version: 1.4
* Author: Kevin Walker
* Author URI: http://walkeezy.ch
* Text Domain: simplistic-seo
* Domain Path: /lang
* License: GPL2
*/


// LOAD LANGUAGE
//-----------------------------------------------------------------------

add_action('plugins_loaded', 'sseo_plugin_init');

function sseo_plugin_init() {
	load_plugin_textdomain( 'simplistic-seo', false, dirname(plugin_basename(__FILE__)).'/lang/' );
}


// AJAX ACTIONS
//-----------------------------------------------------------------------

add_action('wp_ajax_generate_title', 'sseo_ajax_generate_title');

function sseo_ajax_generate_title() {
	$titlestring = sanitize_text_field($_POST['string']);
	$titlepageid = sanitize_key($_POST['pageid']);
	echo sseo_generate_title($titlestring, $titlepageid);
	exit();
}


// GENERATE TITLE & DESCRIPTION
//-----------------------------------------------------------------------

function sseo_generate_title($title, $pageid = NULL) {

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

function sseo_generate_metadescription($postid) {

	$content = get_post_field('post_content', $postid);

	// Strip headings h1-h6
	$content = preg_replace('/<h[1-6][^>]*>([\s\S]*?)<\/h[1-6][^>]*>/', '', $content);
	// Strip line breaks
	$content = preg_replace('/\r|\n/', '', $content);
	// Strip all remaining tags
	$content = wp_strip_all_tags($content);
	// Check if there is a description...
	if(empty($content)){
		return '';
	} else {
		// Limit to 152 characters
		$content = substr($content, 0, 152);
		// Add "..." to the end of the string
		$content .= '...';

		return $content;
	}
}


// ADD METATAGS TO THE HEAD
//-----------------------------------------------------------------------

function sseo_title() {
	global $post;
	if($post){
		// Get title from post meta
		$sseo_title_string = get_post_meta($post->ID, '_sseo_title', true);
		// If empty, get default title pattern
		if(empty($sseo_title_string)) {
			$sseo_title_string = esc_attr(get_option('sseo_title_pattern', '{pagetitle} – {sitetitle}'));
		}
		$sseo_title = sseo_generate_title($sseo_title_string);
		return $sseo_title;
	}
}

add_filter('pre_get_document_title', 'sseo_title', 10, 1);

function sseo_twitter() {
  echo '<meta name="twitter:card" content="summary"/>'."\n";
  $sseo_twittertitle = sseo_title();
  if(!empty($sseo_twittertitle)){
    echo '<meta name="twitter:title" content="'.esc_attr($sseo_twittertitle).'"/>'."\n";
  }
}

if(esc_attr(get_option('sseo_activate_twittercard'))){
  add_filter( 'wp_head', 'sseo_twitter', 1 );
}

function sseo_metadescription() {
	global $post;
	if($post){
		// Get description from post meta
		$sseo_description = get_post_meta($post->ID, '_sseo_metadescription', true);
		// If empty, get default meta description
		if(empty($sseo_description)) {
			$sseo_description = sseo_generate_metadescription($post->ID);
		}
		if(!empty($sseo_description)){
      echo '<meta name="description" content="'.esc_attr($sseo_description).'"/>'."\n";
      if(esc_attr(get_option('sseo_activate_twittercard'))){
        echo '<meta name="twitter:description" content="'.esc_attr($sseo_description).'"/>'."\n";
      }
		}
	}
}

add_filter( 'wp_head', 'sseo_metadescription', 1 );


// ADD CSS TO THE ADMIN
//-----------------------------------------------------------------------

function sseo_adminassets() {
	// CSS
	wp_register_style( 'sseo_admin_css', plugin_dir_url( __FILE__ ) . 'dist/styles.min.css', false, '1' );
	wp_enqueue_style( 'sseo_admin_css' );
	// JS
	wp_register_script( 'sseo_admin_js', plugin_dir_url( __FILE__ ) . 'dist/functions.min.js', false, '1' );
	wp_enqueue_script( 'sseo_admin_js' );
}

add_action( 'admin_enqueue_scripts', 'sseo_adminassets' );


// SETTINGS PAGE
//-----------------------------------------------------------------------

function sseo_adminmenu() {
	add_options_page(__('SEO settings', 'simplistic-seo'), __('SEO settings', 'simplistic-seo'), 'manage_options', 'seo_settings', 'sseo_settingspage');
}

add_action( 'admin_menu', 'sseo_adminmenu' );

function sseo_settingspage() { ?>

	<div class="wrap">
		<h1><?php _e('SEO settings', 'simplistic-seo'); ?></h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'sseo_settings' );
			do_settings_sections( 'sseo_settings' ); ?>

			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<label for="sseo_title_pattern"><?php _e('Title', 'simplistic-seo'); ?></label>
						</th>
						<td>
							<input name="sseo_title_pattern" type="text" class="regular-text" id="sseo_title_pattern" value="<?php echo esc_attr(get_option('sseo_title_pattern', '{pagetitle} – {sitetitle}')); ?>" />
							<p class="description"><?php _e('The title will be generated following this pattern, if there is no other title specified for a post or page.', 'simplistic-seo'); ?></p>
							<p class="description"><?php _e('Placeholder:', 'simplistic-seo'); ?> <a class="sseo-input-placeholder" data-placeholder="{sitetitle}" data-target="sseo_title_pattern"><?php _e('Sitetitle', 'simplistic-seo'); ?></a><a class="sseo-input-placeholder" data-placeholder="{sitedesc}" data-target="sseo_title_pattern"><?php _e('Sitedescription', 'simplistic-seo'); ?></a><a class="sseo-input-placeholder" data-placeholder="{pagetitle}" data-target="sseo_title_pattern"><?php _e('Pagetitle', 'simplistic-seo'); ?></a></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Sitemap XML', 'simplistic-seo'); ?></th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><span><?php _e('Sitemap XML', 'simplistic-seo'); ?></span></legend>
								<label for="sseo_activate_sitemap">
									<input name="sseo_activate_sitemap" type="checkbox" id="sseo_activate_sitemap" value="1" <?php checked( 1, get_option( 'sseo_activate_sitemap' ), true ); ?> >
									<?php _e('Generate sitemap.xml automatically', 'simplistic-seo'); ?>
								</label>
								<?php if(file_exists(ABSPATH . "sitemap.xml")) { ?>
									<p><?php _e('Sitemap URL:', 'simplistic-seo'); ?> <a href="<?php echo esc_url(bloginfo('url') . '/sitemap.xml'); ?>" target="_blank"><?php echo esc_url(bloginfo('url') . '/sitemap.xml'); ?></a></p>
								<?php } ?>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Twitter cards', 'simplistic-seo'); ?></th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><span><?php _e('Twitter cards', 'simplistic-seo'); ?></span></legend>
								<label for="sseo_activate_twittercard">
									<input name="sseo_activate_twittercard" type="checkbox" id="sseo_activate_twittercard" value="1" <?php checked( 1, get_option( 'sseo_activate_twittercard' ), true ); ?> >
									<?php _e('Enable Twitter cards', 'simplistic-seo'); ?>
								</label>
							</fieldset>
						</td>
					</tr>
				</tbody>
			</table>

			<?php submit_button(); ?>

		</form>
	</div>

<?php }

function seo_register_settings() {
	register_setting( 'sseo_settings', 'sseo_title_pattern' );
  register_setting( 'sseo_settings', 'sseo_activate_sitemap' );
  register_setting( 'sseo_settings', 'sseo_activate_twittercard' );
}

add_action( 'admin_init', 'seo_register_settings' );


// METABOX
//-----------------------------------------------------------------------

function sseo_register_metabox() {
	// Get all custom post types
	$post_types = get_post_types( array('_builtin' => false), 'names', 'and');
	// Add standard post types
	$posttypes_array = array('post', 'page');
	foreach ($post_types  as $post_type ) {
		$posttypes_array[] = $post_type;
	}
	add_meta_box( 'sseo-metabox', __('SEO settings', 'simplistic-seo'), 'sseo_render_metabox', $posttypes_array, 'normal', 'low' );
}

add_action( 'add_meta_boxes', 'sseo_register_metabox' );

function sseo_render_metabox() {

	global $post;
  $values = get_post_custom( $post->ID );
	$sseo_title = isset( $values['_sseo_title'] ) ? $values['_sseo_title'][0] : '';
	$sseo_title_default_string = esc_attr(get_option('sseo_title_pattern', '{pagetitle} – {sitetitle}'));
	$sseo_title_default = sseo_generate_title($sseo_title_default_string);
	$sseo_metadescription = isset( $values['_sseo_metadescription'] ) ? $values['_sseo_metadescription'][0] : '';
	$sseo_metadescription_default = sseo_generate_metadescription($post->ID, 'content');
	wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' ); ?>

	<div id="sseo-meta-editor">
		<p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="sseo-title"><?php _e('Title', 'simplistic-seo'); ?></label><span id="sseo-title-info" class="length-info"></span></p>
		<input type="text" name="sseo-title" id="sseo-title" value="<?php echo $sseo_title; ?>" />
		<div class="sseo-settings-input-placeholders"><p><?php _e('Placeholder:', 'simplistic-seo'); ?> <a class="sseo-input-placeholder" data-placeholder="{sitetitle}" data-target="sseo-title"><?php _e('Sitetitle', 'simplistic-seo'); ?></a><a class="sseo-input-placeholder" data-placeholder="{sitedesc}" data-target="sseo-title"><?php _e('Sitedescription', 'simplistic-seo'); ?></a><a class="sseo-input-placeholder" data-placeholder="{pagetitle}" data-target="sseo-title"><?php _e('Pagetitle', 'simplistic-seo'); ?></a></p></div>
		<input type="hidden" name="sseo-pageid" id="sseo-pageid" value="<?php echo $_GET['post']; ?>" />
		<input type="hidden" name="sseo-title-default" id="sseo-title-default" value="<?php echo $sseo_title_default; ?>" />
		<p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="sseo-metadescription"><?php _e('Metadescription', 'simplistic-seo'); ?></label><span id="sseo-metadescription-info" class="length-info"></span></p>
		<textarea name="sseo-metadescription" class="postbox" id="sseo-metadescription"><?php echo $sseo_metadescription; ?></textarea>
		<input type="hidden" name="sseo-metadescription-default" id="sseo-metadescription-default" value="<?php echo $sseo_metadescription_default; ?>" />
	</div>
	<div id="sseo-preview">
		<p class="post-attributes-label-wrapper post-attributes-label"><?php _e('Preview', 'simplistic-seo'); ?></p>
		<div id="sseo-google-preview-wrapper">
			<span id="sseo-preview-title"><?php if(!empty($sseo_title)): echo sseo_generate_title($sseo_title); else: echo $sseo_title_default; endif; ?></span>
			<span id="sseo-preview-url"><?php the_permalink(); ?><span id="sseo-preview-url-arrow"></span></span>
			<span id="sseo-preview-metadescription"><?php if(!empty($sseo_metadescription)): echo $sseo_metadescription; else: echo $sseo_metadescription_default; endif; ?></span>
		</div>
	</div>
	<div class="clear"></div>

<?php }

function sseo_save_metabox($post_id) {
	// Bail if we're doing an auto save
  if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
  // if our nonce isn't there, or we can't verify it, bail
  if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' ) ) return;
  // if our current user can't edit this post, bail
  if( !current_user_can( 'edit_post' ) ) return;

	if( isset( $_POST['sseo-title'] ) )
		update_post_meta( $post_id, '_sseo_title', esc_html( $_POST['sseo-title'] ) );

  if( isset( $_POST['sseo-metadescription'] ) )
		update_post_meta( $post_id, '_sseo_metadescription', esc_html( $_POST['sseo-metadescription'] ) );
}

add_action( 'save_post', 'sseo_save_metabox' );


// SITEMAP
//-----------------------------------------------------------------------

function sseo_generate_sitemap() {

	$sitemap = '';

	if ( str_replace( '-', '', get_option( 'gmt_offset' ) ) < 10 ) {
		$tempo = '-0' . str_replace( '-', '', get_option( 'gmt_offset' ) );
	} else {
		$tempo = get_option( 'gmt_offset' );
	}

	if( strlen( $tempo ) == 3 ) {
		$tempo = $tempo . ':00';
	}

	$postsForSitemap = get_posts(array( 'numberposts' => -1, 'orderby' => 'modified', 'post_type' => 'any', 'order' => 'DESC' ));
	$sitemap .= '<?xml version="1.0" encoding="UTF-8"?>' . '<?xml-stylesheet type="text/xsl" href="' . esc_url( home_url( '/' ) ) . 'sitemap.xsl"?>';
	$sitemap .= "\n" . '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
	$sitemap .= "\t" . '<url>' . "\n" .
	"\t\t" . '<loc>' . esc_url( home_url( '/' ) ) . '</loc>' .
	"\n\t\t" . '<lastmod>' . date( "Y-m-d\TH:i:s", current_time( 'timestamp', 0 ) ) . $tempo . '</lastmod>' .
	"\n\t" . '</url>' . "\n";

	foreach( $postsForSitemap as $post ) {
		setup_postdata( $post);
		$postdate = explode( " ", $post->post_modified );
		$sitemap .= "\t" . '<url>' . "\n" .
		"\t\t" . '<loc>' . get_permalink( $post->ID ) . '</loc>' .
		"\n\t\t" . '<lastmod>' . $postdate[0] . 'T' . $postdate[1] . $tempo . '</lastmod>' .
		"\n\t" . '</url>' . "\n";
	}

	$sitemap .= '</urlset>';
	$fp = fopen( ABSPATH . "sitemap.xml", 'w' );
	fwrite( $fp, $sitemap );
	fclose( $fp );
}

function sseo_delete_sitemap() {
	if(file_exists(ABSPATH . "sitemap.xml")) {
		unlink (ABSPATH . "sitemap.xml");
	}
}

$option_name = 'sseo_activate_sitemap';

add_action('added_option', function( $option_name ) {

	$sitemapactivated = esc_attr(get_option('sseo_activate_sitemap'));

	// Generate or delete sitemap, depending on settings
	if($sitemapactivated) {
		sseo_generate_sitemap();
	} else {
		sseo_delete_sitemap();
	}

}, 10, 2);

add_action( 'save_post', function() {

	$sitemapactivated = esc_attr(get_option('sseo_activate_sitemap'));

	// Generate or delete sitemap, depending on settings
	if($sitemapactivated) {
		sseo_generate_sitemap();
	} else {
		sseo_delete_sitemap();
	}

} );

?>
