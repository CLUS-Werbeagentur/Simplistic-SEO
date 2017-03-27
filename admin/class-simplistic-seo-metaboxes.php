<?php

/**
 * The metabox-specific functionality of the plugin.
 *
 * @link       http://walkeezy.ch
 * @since      1.0.0
 *
 * @package    Simplistic_Seo
 * @subpackage Simplistic_Seo/admin
 */

/**
 * The metabox-specific functionality of the plugin.
 *
 * @package    Simplistic_Seo
 * @subpackage Simplistic_Seo/admin
 * @author     Kevin Walker <mail@kevinwalker.ch>
 */
class Simplistic_Seo_Admin_Metaboxes {

	/**
	 * The post meta data
	 *
	 * @since 		1.0.0
	 * @access 		private
	 * @var 		string 			$meta    			The post meta data.
	 */
	private $meta;

	/**
	 * The ID of this plugin.
	 *
	 * @since 		1.0.0
	 * @access 		private
	 * @var 		string 			$plugin_name 		The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since 		1.0.0
	 * @access 		private
	 * @var 		string 			$version 			The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 		1.0.0
	 * @param 		string 			$plugin_name 		The name of this plugin.
	 * @param 		string 			$version 			The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Registers metaboxes with WordPress
	 *
	 * @since 	1.0.0
	 * @access 	public
	 */
	public function add_metaboxes() {

        add_meta_box( 'meta-box-id', __( 'My Meta Box', 'textdomain' ), 'wpdocs_my_display_callback', 'post' );
  }

} // class
