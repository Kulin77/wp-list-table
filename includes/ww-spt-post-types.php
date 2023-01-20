<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

//creating custom post type
add_action( 'init', 'ww_spt_reg_create_post_type'); //creating custom post

/**
 * Register Post Type
 *
 * Register Custom Post Type for managing registered taxonomy
 *
 * @package WP List Table (WP Table)
 * @since 1.0.0
 */
function ww_spt_reg_create_post_type() {
	
	$labels = array(
					    'name'				=> esc_html__('Products','wwspt'),
					    'singular_name' 	=> esc_html__('Product','wwspt'),
					    'add_new' 			=> esc_html__('Add New','wwspt'),
					    'add_new_item' 		=> esc_html__('Add New Product','wwspt'),
					    'edit_item' 		=> esc_html__('Edit Product','wwspt'),
					    'new_item' 			=> esc_html__('New Product','wwspt'),
					    'all_items' 		=> esc_html__('All Products','wwspt'),
					    'view_item' 		=> esc_html__('View Product','wwspt'),
					    'search_items' 		=> esc_html__('Search Product','wwspt'),
					    'not_found' 		=> esc_html__('No products found','wwspt'),
					    'not_found_in_trash'=> esc_html__('No products found in Trash','wwspt'),
					    'parent_item_colon' => '',
					    'menu_name' => esc_html__('Products','wwspt'),
					);
	$args = array(
				    'labels' 			=> $labels,
				    'public' 			=> false,
				    'query_var' 		=> false,
				    'rewrite' 			=> false, //array( 'slug' => WW_SPT_POST_TYPE ),
				    'capability_type' 	=> WW_SPT_POST_TYPE,
				    'hierarchical' 		=> false,
				    'map_meta_cap'      => true,
				    'supports' 			=> array( 'title')
			  ); 
	
	register_post_type( WW_SPT_POST_TYPE,$args);
}