<?php
/**
 * Plugin Name: WP List Table (WP Table)
 * Plugin URI: https://github.com/rids1207
 * Description: This plugin handles Add,Edit and Delete functinalities with <strong>WordPress post Table</strong> and Listing is managed via Wp_List_Table. 
 * Version: 1.0.0
 * Author: Rids
 * Author URI: https://github.com/rids1207
 * Text Domain: wwspt
 * Domain Path: languages
 * 
 * @package WP List Table (WP Table)
 * @category Core
 * @author Rids
 */

/**
 * Define Some needed predefined variables
 * 
 * @package WP List Table (WP Table)
 * @since 1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Basic Plugin Definitions 
 * 
 * @package WP List Table (WP Table)
 * @since 1.0.0
 */
if( !defined( 'WW_SPT_VERSION' ) ) {
	define( 'WW_SPT_VERSION', '1.0.0' ); //version of plugin
}
if( !defined( 'WW_SPT_DIR' ) ) {
	define( 'WW_SPT_DIR', dirname( __FILE__ ) ); // plugin dir
}
if( !defined( 'WW_SPT_ADMIN' ) ) {
	define( 'WW_SPT_ADMIN', WW_SPT_DIR . '/includes/admin' ); // plugin admin dir
}
if(!defined('wwsptlevel')) { //check if variable is not defined previous then define it
	define('wwsptlevel','manage_options'); // this is capability in plugin
}
if(!defined('WW_SPT_URL')) {
	define('WW_SPT_URL',plugin_dir_url( __FILE__ ) ); // plugin url
}
if(!defined('WW_SPT_POST_TYPE')) {
	define('WW_SPT_POST_TYPE', 'ww_spt');
}
//metabox prefix
if( !defined( 'WW_SPT_META_PREFIX' )) {
	define( 'WW_SPT_META_PREFIX', '_ww_spt_' );
}
if( !defined( 'WW_SPT_PLUGIN_BASENAME' ) ) {
	define( 'WW_SPT_PLUGIN_BASENAME', basename( WW_SPT_DIR ) ); //Plugin base name
}
/**
 * Load Text Domain
 * 
 * This gets the plugin ready for translation.
 * 
 * @package WP List Table (WP Table)
 * @since 1.0.0
 */
function ww_spt_load_textdomain() {
	
 // Set filter for plugin's languages directory
	$ww_spt_lang_dir	= dirname( plugin_basename( __FILE__ ) ) . '/languages/';
	$ww_spt_lang_dir	= apply_filters( 'ww_spt_languages_directory', $ww_spt_lang_dir );
	
	// Traditional WordPress plugin locale filter
	$locale	= apply_filters( 'plugin_locale',  get_locale(), 'wwspt' );
	$mofile	= sprintf( '%1$s-%2$s.mo', 'wwspt', $locale );
	
	// Setup paths to current locale file
	$mofile_local	= $ww_spt_lang_dir . $mofile;
	$mofile_global	= WP_LANG_DIR . '/' . WW_SPT_PLUGIN_BASENAME . '/' . $mofile;
	
	if ( file_exists( $mofile_global ) ) { // Look in global /wp-content/languages/wp-list-table(wp-table) folder
		load_textdomain( 'wwspt', $mofile_global );
	} elseif ( file_exists( $mofile_local ) ) { // Look in local /wp-content/plugins/wp-list-table(wp-table)/languages/ folder
		load_textdomain( 'wwspt', $mofile_local );
	} else { // Load the default language files
		load_plugin_textdomain( 'wwspt', false, $ww_spt_lang_dir );
	}
  
}
/**
 * Plugin Activation hook
 * 
 * This hook will call when plugin will activate
 * 
 * @package WP List Table (WP Table)
 * @since 1.0.0
 */
register_activation_hook( __FILE__, 'ww_spt_install' );

function ww_spt_install() {
	
	global $wpdb;
	
	//register post type
	ww_spt_reg_create_post_type();
	
	//IMP Call of Function
	//Need to call when custom post type is being used in plugin
	flush_rewrite_rules();
}


/**
 * Plugin Deactivation hook
 * 
 * This hook will call when plugin will deactivate
 * 
 * @package WP List Table (WP Table)
 * @since 1.0.0
 */
register_deactivation_hook( __FILE__, 'ww_spt_uninstall' );

function ww_spt_uninstall() {
	
	global $wpdb;	
}

/**
 * Load Plugin
 * 
 * Handles to load plugin after
 * dependent plugin is loaded
 * successfully
 * 
 * @package WP List Table (WP Table)
 * @since 1.0.0
 */
function ww_spt_plugin_loaded() {
 
	// load first plugin text domain
	ww_spt_load_textdomain();
}

//add action to load plugin
add_action( 'plugins_loaded', 'ww_spt_plugin_loaded' );

/**
 * Includes Class Files
 * 
 * @package WP List Table (WP Table)
 * @since 1.0.0
 */
global $ww_spt_model,$ww_spt_scripts,$ww_spt_admin;

//includes post types file
include_once( WW_SPT_DIR . '/includes/ww-spt-post-types.php');

//includes scripts class file
require_once ( WW_SPT_DIR .'/includes/class-ww-spt-scripts.php');
$ww_spt_scripts = new Ww_Spt_Scripts();
$ww_spt_scripts->add_hooks();

//includes model class
require_once( WW_SPT_DIR . '/includes/class-ww-spt-model.php' );
$ww_spt_model = new Ww_Spt_Model();

//includes admin pages
require_once( WW_SPT_ADMIN . '/class-ww-spt-admin.php' );
$ww_spt_admin = new Ww_Spt_Admin_Pages();
$ww_spt_admin->add_hooks();