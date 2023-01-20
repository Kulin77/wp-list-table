<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Admin Pages Class
 * 
 * Handles all admin functinalities
 *
 * @package WP List Table (WP Table)
 * @since 1.0.0
 */
class Ww_Spt_Admin_Pages{
	
	public $model, $scripts;
	
	public function __construct(){
		
		global $ww_spt_model, $ww_spt_scripts;
		$this->model = $ww_spt_model;
		$this->scripts = $ww_spt_scripts;
		
	}
	
	/**
	 * Add Top Level Menu Page
	 *
	 * Runs when the admin_menu hook fires and adds a new
	 * top level admin page and menu item
	 * 
	 * @package WP List Table (WP Table)
	 * @since 1.0.0
	 */
	public function ww_spt_admin_menu() {

		//main menu page	
		add_menu_page( esc_html__('WP List Table (WP Table)','wwspt'),esc_html__('WP List Table (WP Table)','wwspt'),wwsptlevel, 'ww_spt_products', '');

		add_submenu_page( 'ww_spt_products', esc_html__('Products','wwspt'), esc_html__('Products','wwspt'), wwsptlevel, 'ww_spt_products', array($this,'ww_spt_add_submenu_list_table_page') );
		
		$spt_admin_add_page = add_submenu_page( 'ww_spt_products', esc_html__('Products','wwspt'), esc_html__('Add New','wwspt'), wwsptlevel, 'ww_spt_add_products', array($this,'ww_spt_add_submenu_page') );
		
		//loads javascript needed for add page for toggle metaboxes
		add_action( "admin_head-$spt_admin_add_page", array( $this->scripts, 'ww_spt_add_product_page_load_scripts' ) );
	}
	
	/**
	 * List of all Product
	 *
	 * Handles Function to listing all product
	 * 
	 * @package WP List Table (WP Table)
	 * @since 1.0.0
	 */
	public function ww_spt_add_submenu_list_table_page() {
		
		include_once( WW_SPT_ADMIN . '/forms/ww-spt-product-list.php' );
		
	}
	
	/**
	 * Adding Admin Sub Menu Page
	 *
	 * Handles Function to adding add data form
	 * 
	 * @package WP List Table (WP Table)
	 * @since 1.0.0
	 */
	public function ww_spt_add_submenu_page() {
		
		include_once( WW_SPT_ADMIN . '/forms/ww-spt-add-edit-product.php' );
		
	}
	
	/**
	 * Add action admin init
	 * 
	 * Handles add and edit functionality of product
	 * 
	 * @package WP List Table (WP Table)
	 * @since 1.0.0
	 */
	public function ww_spt_admin_init() {
		include_once( WW_SPT_ADMIN . '/forms/ww-spt-product-save.php' );
	}
	
	/**
	 * Bulk Delete
	 * 
	 * Handles bulk delete functinalities of product
	 * 
	 * @package WP List Table (WP Table)
	 * @since 1.0.0
	 */
	public function ww_spt_admin_bulk_delete() {
		
		if(((isset( $_GET['action'] ) && $_GET['action'] == 'delete' ) || (isset( $_GET['action2'] ) && $_GET['action2'] == 'delete' )) && isset($_GET['page']) && $_GET['page'] == 'ww_spt_products' ) { //check action and page
		
			// Get redirect url
			$redirect_url = add_query_arg( array( 'page' => 'ww_spt_products' ), admin_url( 'admin.php' ) );

			//get bulk product array from $_GET
			$action_on_id = $_GET['product'];
			
			if( count( $action_on_id ) > 0 ) { //check there is some checkboxes are checked or not 
				
				//if there is multiple checkboxes are checked then call delete in loop
				foreach ( $action_on_id as $spt_id ) {
					
					//parameters for delete function
					$args = array (
									'spt_id' => $spt_id
								);
								
					//call delete function from model class to delete records
					$this->model->ww_spt_bulk_delete( $args );
				}
				
				$redirect_url = add_query_arg( array( 'message' => '3' ), $redirect_url );
				
				//if bulk delete is performed successfully then redirect 
				wp_redirect( $redirect_url ); 
				exit;
			} else {
				//if there is no checboxes are checked then redirect to listing page
				wp_redirect( $redirect_url ); 
				exit;
			}			
		}
	}
	
	/**
	 * Status Change
	 * 
	 * Handles changing status of product
	 * 
	 * @package WP List Table (WP Table)
	 * @since 1.0.0
	 */
	public function ww_spt_admin_change_status() {
		
		$prefix = WW_SPT_META_PREFIX;
		
		if (isset($_GET['spt_status']) && isset($_GET['spt_id']) && !empty($_GET['spt_id'])) {
			
			// Get redirect url
			$redirect_url = add_query_arg( array( 'page' => 'ww_spt_products', 'message' => '4' ), admin_url( 'admin.php' ) );

			$postid = $_GET['spt_id'];
			update_post_meta( $postid, $prefix.'product_status',$_GET['spt_status']);
			
			wp_redirect( $redirect_url ); 
			exit;
			
		} 
		
	}
	
	
	/**
	 * Adding Hooks
	 *
	 * @package WP List Table (WP Table)
	 * @since 1.0.0
	 */
	public function add_hooks() {
		
		//add new admin menu page
		add_action('admin_menu',array($this,'ww_spt_admin_menu'));
		
		//add admin init for saving data
		add_action( 'admin_init' , array($this,'ww_spt_admin_init'));
		
		//add admin init for bulk delete functionality
		add_action( 'admin_init' , array($this,'ww_spt_admin_bulk_delete'));
		
		//add admin init for changing status
		add_action( 'admin_init' , array($this,'ww_spt_admin_change_status'));
		
	}
}
