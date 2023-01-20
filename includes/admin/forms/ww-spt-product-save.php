<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Save products
 *
 * Handle product save and edit products
 * 
 * @package WP List Table (WP Table)
 * @since 1.0.0
 */

	global $errmsg, $wpdb, $user_ID,$error,$ww_spt_model;

	$model = $ww_spt_model;
	$prefix = WW_SPT_META_PREFIX;
	// save for product data
	if(isset($_POST['ww_spt_product_save']) && !empty($_POST['ww_spt_product_save'])) { //check submit button click

		$error = '';
		
		if(isset($_POST['ww_spt_product_title']) && empty($_POST['ww_spt_product_title'])) { //check product title
			
			$errmsg['product_title'] = esc_html__('Please Enter Product title.','wwspt');
			$error = true;
		}
		if(isset($_POST['ww_spt_product_desc']) && empty($_POST['ww_spt_product_desc'])) { //check product content
			
			$errmsg['product_desc'] = esc_html__('Please Enter Product description.','wwspt');
			$error = true;
		}
		
		if(isset($_POST['ww_spt_product_avail']) && !empty($_POST['ww_spt_product_avail'])) { //check product availibility
			
			$spt_available = implode(',', $_POST['ww_spt_product_avail']);
			
		}
		
		if(isset($_GET['spt_id']) && !empty($_GET['spt_id']) && $error != true) { //check no error and product id is set in url
			
			$postid = $_GET['spt_id'];
			
			//data needs to update for product
			$update_post = array(
									'ID'			=> $postid,
									'post_title'    =>	$_POST['ww_spt_product_title'],
									'post_content'  =>	$_POST['ww_spt_product_desc'],
									'post_status'   =>	'publish',
									'post_author'   =>	$user_ID,
								);
			
			//update product data
			wp_update_post( $model->ww_spt_escape_slashes_deep($update_post) );
			//update_post_meta( $postid, $prefix.'product_status',$_POST['ww_spt_product_status']);
			update_post_meta( $postid, $prefix.'product_avail',isset($_POST['ww_spt_product_avail']) ? $_POST['ww_spt_product_avail'] : array());
			update_post_meta( $postid, $prefix.'featured_product',$_POST['ww_spt_featured_product']);
			update_post_meta( $postid, $prefix.'product_price',isset($_POST['ww_spt_product_price']) ? $model->ww_spt_escape_slashes_deep($_POST['ww_spt_product_price']) : '');
			
			// Get redirect url
			$redirect_url = add_query_arg( array( 'page' => 'ww_spt_products', 'message' => '2' ), admin_url( 'admin.php' ) );

			wp_redirect( $redirect_url );
			exit;
			
		} else {
		
			if($error != true) { //check there is no error then insert data in to the table
				
				// Create post object
				$product_arr = array(
									  'post_title'    =>	$_POST['ww_spt_product_title'],
									  'post_content'  =>	$_POST['ww_spt_product_desc'],
									  'post_status'   =>	'publish',
									  'post_author'   =>	$user_ID,
									  'post_type'     =>	WW_SPT_POST_TYPE
									);
				
				// Insert the post into the database
				$result = wp_insert_post( $model->ww_spt_escape_slashes_deep($product_arr)  );
				
				if($result) { //check inserted product id
					
					//update_post_meta( $result, $prefix.'product_status',$_POST['ww_spt_product_status']);
					update_post_meta( $result, $prefix.'product_avail',isset($_POST['ww_spt_product_avail']) ? $_POST['ww_spt_product_avail'] : array());
					update_post_meta( $result, $prefix.'featured_product',$_POST['ww_spt_featured_product']);
					update_post_meta( $result, $prefix.'product_price',isset($_POST['ww_spt_product_price']) ? $model->ww_spt_escape_slashes_deep($_POST['ww_spt_product_price']) : '');
				
					// Get redirect url
					$redirect_url = add_query_arg( array( 'page' => 'ww_spt_products', 'message' => '1' ), admin_url( 'admin.php' ) );

					wp_redirect( $redirect_url );
					exit;
					
				}
			}
		}
	}
?>