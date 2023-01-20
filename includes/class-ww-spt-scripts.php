<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Scripts Class
 *
 * Handles adding scripts functionality to the admin pages
 * as well as the front pages.
 *
 * @package WP List Table (WP Table) 
 * @since 1.0.0
 */
class Ww_Spt_Scripts {
	
	public function __construct() {
		
		
	}
	
	/**
	 * Loading Additional Java Script
	 *
	 * Loads the JavaScript required for toggling the meta boxes on the theme settings page
	 *
	 * @package WP List Table (WP Table)
	 * @since 1.0.0
	 */
	public function ww_spt_add_product_page_load_scripts() { 
		?>
			<script>
				//<![CDATA[
				jQuery(document).ready( function($) {
					$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
					postboxes.add_postbox_toggles( 'toplevel_page_ww_spt_products' );
				});
				//]]>
			</script>
		<?php
	}
	
	/**
	 * Load Some Javascript
	 * 
	 * Load JavaScript for handling functionalities for metaboxes
	 * 
	 * @package WP List Table (WP Table)
	 * @since 1.0.0
	 */
	public function ww_spt_page_print_scripts($hook_suffix) {
		
		if ( $hook_suffix == 'wp-list-table-wp-table_page_ww_spt_add_products' ) {
	
			// loads the required scripts for the meta boxes
			wp_enqueue_script( 'common' );
			wp_enqueue_script( 'wp-lists' );
			wp_enqueue_script( 'postbox' );
	
		}
	}
	
	/**
	 * Enqueuing Styles
	 *
	 * Loads the required stylesheets for displaying
	 *
	 * @package WP List Table (WP Table)
	 * @since 1.0.0
	 */
	public function ww_spt_admin_print_styles() {

		// Register & Enqueue admin style
		wp_register_style( 'ww-spt-admin', WW_SPT_URL . 'includes/css/ww-spt-admin.css', array(), WW_SPT_VERSION );
		wp_enqueue_style( 'ww-spt-admin' );
	}
	
	/**
	 * Adding Hooks
	 *
	 * Adding hooks for the styles and scripts.
	 *
	 * @package WP List Table (WP Table)
	 * @since 1.0.0
	 */
	public function add_hooks() {
		
		//add style sheets
		add_action('admin_enqueue_scripts',array($this,'ww_spt_admin_print_styles'));
		
		//add script for adding some required scripts for metaboxes
		add_action( 'admin_enqueue_scripts', array( $this, 'ww_spt_page_print_scripts' ) );
		
	}
}