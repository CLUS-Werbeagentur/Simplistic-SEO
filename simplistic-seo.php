<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://walkeezy.ch
 * @since             1.0.0
 * @package           Simplistic_Seo
 *
 * @wordpress-plugin
 * Plugin Name:       Simplistic SEO
 * Plugin URI:        http://walkeezy.ch/simplistic-seo
 * Description:       All you need regarding SEO on a WordPress website.
 * Version:           1.0.0
 * Author:            Kevin Walker
 * Author URI:        http://walkeezy.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       simplistic-seo
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-simplistic-seo-activator.php
 */
function activate_simplistic_seo() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-simplistic-seo-activator.php';
	Simplistic_Seo_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-simplistic-seo-deactivator.php
 */
function deactivate_simplistic_seo() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-simplistic-seo-deactivator.php';
	Simplistic_Seo_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_simplistic_seo' );
register_deactivation_hook( __FILE__, 'deactivate_simplistic_seo' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-simplistic-seo.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_simplistic_seo() {

	$plugin = new Simplistic_Seo();
	$plugin->run();

}
run_simplistic_seo();
