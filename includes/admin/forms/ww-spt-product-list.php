<?php

/**
 * Product List Page
 * 
 * The html markup for the product list
 * 
 * @package WP List Table (WP Table)
 * @since 1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// Load WP_List_Table if not loaded
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Ww_Spt_Product_List extends WP_List_Table {
	
	public $model, $per_page;
	
	public function __construct() {
		
        global $ww_spt_model, $page;
        
        // Set parent defaults
        parent::__construct( array(
							            'singular'  => 'product',	//singular name of the listed records
							            'plural'    => 'products',	//plural name of the listed records
							            'ajax'      => false		//does this table support ajax?
							        ) );
		
		$this->model	= $ww_spt_model;
		$this->per_page	= apply_filters( 'ww_spt_posts_per_page', 5 ); // Per page
    }
    
    /**
     * Function to overwrite the search box output
     * 
     * @package WP List Table (WP Table)
	 * @since 1.0.0
     */
    /*public function search_box( $text, $input_id ) {
    	
    	if ( empty( $_REQUEST['s'] ) && !$this->has_items() )
			return;
		
    }*/
    
    /**
     * Display Columns
     * 
     * Handles which columns to show in table
     * 
	 * @package WP List Table (WP Table)
	 * @since 1.0.0
     */
	public function get_columns() {
		
        $columns = array(
	        					'cb'      		=> '<input type="checkbox" />',
					            'post_title'	=> esc_html__( 'Title', 'wwspt' ),
					            'post_content'	=> esc_html__( 'Description', 'wwspt' ),
					            'spt_price'		=> esc_html__( 'Price', 'wwspt' ),
					            'post_date'		=> esc_html__( 'Date', 'wwspt' )
					        );
		
		return apply_filters( 'ww_spt_table_columns', $columns );
    }
    
    /**
     * Sortable Columns
     * 
     * Handles soratable columns of the table
     * 
	 * @package WP List Table (WP Table)
	 * @since 1.0.0
     */
	public function get_sortable_columns() {
		
        $sortable_columns = array(
							            'post_title'	=> array( 'post_title', true ),
							            'post_content'	=> array( 'post_content', true ),
							            'post_date'		=> array( 'post_date', true ),
							            'spt_price'		=> array( 'product_price', true ),
							        );
		
        return apply_filters( 'ww_spt_table_sortable_columns', $sortable_columns );
    }
    
    /**
	 * Mange column data
	 * 
	 * Default Column for listing table
	 * 
	 * @package WP List Table (WP Table)
	 * @since 1.0.0
	 */
	public function column_default( $item, $column_name ) {
		
        switch( $column_name ) {
            case 'post_title':
            case 'post_content':
            case 'spt_price' :
            case 'post_date':
				return $item[ $column_name ];
			default:
				return print_r( $item, true );
        }
    }
    
    /**
	 * Render the checkbox column
	 * 
	 * @package WP List Table (WP Table)
	 * @since 1.0.0
	 */
    public function column_cb( $item ) {
    	
    	$checkbox_html	= '<input type="checkbox" name="%1$s[]" value="%2$s" />';
        return sprintf( $checkbox_html, $this->_args['singular'], $item['ID'] );
    }
    
	/**
	 * Manage Edit/Delete Link
	 * 
	 * Does to show the edit and delete link below the column cell
	 * function name should be column_{field name}
	 * 
	 * @package WP List Table (WP Table)
	 * @since 1.0.0
	 */
	public function column_post_title( $item ) {
		
		// Build row actions
		$actions = array(
			'edit'      => sprintf('<a href="?page=%s&action=%s&spt_id=%s">'.esc_html__('Edit', 'wwspt').'</a>','ww_spt_add_products','edit',$item['ID']),
			'delete'    => sprintf('<a href="?page=%s&action=%s&product[]=%s">'.esc_html__('Delete', 'wwspt').'</a>',$_REQUEST['page'],'delete',$item['ID']),
		);
		
		// Return the title contents
		return sprintf( '%1$s %2$s', $item['post_title'], $this->row_actions( $actions ) );
	}
    
    /**
     * Bulk actions field
     * 
     * Handles Bulk Action combo box values
     * 
	 * @package WP List Table (WP Table)
	 * @since 1.0.0
     */
	public function get_bulk_actions() {
		
		// Bulk action combo box parameter
        $actions	= array( 'delete' => 'Delete' );
		
		return apply_filters( 'ww_spt_table_bulk_actions', $actions );
    }
    
    /**
	 * Process the bulk actions
	 * 
	 * @package WP List Table (WP Table)
	 * @since 1.0.0
	 */
    public function process_bulk_action() {
    	
        // Detect when a bulk action is being triggered...
        if( 'delete'===$this->current_action() ) {
        	wp_die(esc_html__( 'Items deleted (or they would be if we had items to delete)!', 'wwspt' ));
        }
    }
    
    /**
	 * Display message when there is no items
	 * 
	 * @package WP List Table (WP Table)
	 * @since 1.0.0
	 */
    public function no_items() {
		// Message to show when no records in database table
		esc_html_e( 'No products found.', 'wwspt' );
	}
    
    /**
	 * Displaying Prodcuts
	 * 
	 * Does prepare the data for displaying the products in the table.
	 * 
	 * @package WP List Table (WP Table)
	 * @since 1.0.0
	 */
	public function display_products() {
		
		$prefix 	= WW_SPT_META_PREFIX;
		$resultdata = array();
		
		// Taking parameter
		$orderby 	= isset( $_GET['orderby'] )	? urldecode( $_GET['orderby'] )		: 'id';
		$order		= isset( $_GET['order'] )	? $_GET['order']                	: 'DESC';
		$search 	= isset( $_GET['s'] ) 		? sanitize_text_field( trim($_GET['s']) )	: null;
		
		$args = array(
						'posts_per_page'	=> $this->per_page,
						'page'				=> isset( $_GET['paged'] ) ? $_GET['paged'] : null,
						'orderby'			=> $orderby,
						'order'				=> $order,
						'offset'  			=> ( $this->get_pagenum() - 1 ) * $this->per_page
					);
		
		//Orderby Meta Query
		switch ( $orderby ) {
			
			case 'product_price' :
				$args['orderby'] = 'meta_value_num';
				$args['meta_key'] = $prefix . 'product_price';
			break;
		}
		
		// If search is call then pass searching value to function for displaying searching values
		if( is_string( $search ) ) {
			
			$search_param 	= explode(':', $search);
			$search_act 	= isset($search_param[0]) ? strtolower(trim($search_param[0])) : '';
			$search_value 	= isset($search_param[1]) ? trim($search_param[1]) : $search;
			
			switch ( $search_act ) {
				case 'price':
					$args['meta_query'] = array(
						array(
							'key'     => $prefix . 'product_price',
							'value'   => $search_value,
							'compare' => '=',
						));
			}
			
			// If meta query is not there then make search param
			if( empty($args['meta_query']) ) {
				$args['s'] = $search;
			}
		}
		
		// Function to retrive data
		$data = $this->model->ww_spt_get_coupons( $args );
		
		if( !empty($data['data']) ) {
			
			// Re generate data
			foreach ($data['data'] as $key => $value) {
				$resultdata[$key]['ID'] 			= $value['ID'];
				$resultdata[$key]['post_title'] 	= $value['post_title'];
				$resultdata[$key]['post_content'] 	= $value['post_content'];
				$resultdata[$key]['spt_price'] 		= get_post_meta($value['ID'],$prefix.'product_price',true);
				$resultdata[$key]['post_date'] 		= date_i18n( get_option('date_format'). ' '. get_option('time_format') ,strtotime($value['post_date']));
			}
		}
		
		$result_arr['data']		= !empty($resultdata) 	? $resultdata 		: array();
		$result_arr['total'] 	= isset($data['total']) ? $data['total'] 	: ''; // Total no of data
		
		return $result_arr;
	}
	
	/**
	 * Setup the final data for the table
	 * 
	 * @package WP List Table (WP Table)
	 * @since 1.0.0
	 */
	public function prepare_items() {
        
        // Get how many records per page to show
        $per_page	= $this->per_page;
        
        // Get All, Hidden, Sortable columns
        $columns	= $this->get_columns();
        $hidden		= array();
		$sortable	= $this->get_sortable_columns();
        
		// Get final column header
		$this->_column_headers	= array( $columns, $hidden, $sortable );
        
		// Proces bulk action
		$this->process_bulk_action();
        
		// Get Data of particular page
		$data_res 	= $this->display_products();
		$data 		= $data_res['data'];
       	
		// Get current page number
		$current_page	= $this->get_pagenum();
		
		// Get total count
        $total_items	= $data_res['total'];
		
        // Get page items
        $this->items	= $data;
        
		// We also have to register our pagination options & calculations.
		$this->set_pagination_args( array(
										'total_items' => $total_items,
										'per_page'    => $per_page,
										'total_pages' => ceil($total_items/$per_page)
									) );
    }
}

// Create an instance of our package class...
$ProductListTable = new Ww_Spt_Product_List();

// Fetch, prepare, sort, and filter our data...
$ProductListTable->prepare_items();

// Creating page link
$manage_product_page = add_query_arg( array( 'page' => 'ww_spt_add_products' ), admin_url( 'admin.php' ) );
?>

<!-- List Table Output Starts Here -->
<div class="wrap ww-spt-wrap">
    <h2><?php 
    	esc_html_e( 'Products', 'wwspt' ); ?>
    	<a class="add-new-h2" href="<?php echo $manage_product_page; ?>"><?php esc_html_e( 'Add New','wwspt' ); ?></a>
    </h2><?php
		
    	if( isset($_GET['message']) && !empty($_GET['message'] ) ) { //check message
			if( $_GET['message'] == '1' ) { // Check insert message
				$msg = esc_html__("Product Inserted Successfully.",'wwspt');
			} else if($_GET['message'] == '2') { // Check update message
				$msg = esc_html__("Product Updated Successfully.",'wwspt');
			} else if($_GET['message'] == '3') { // Check delete message
				$msg = esc_html__("Product deleted Successfully.",'wwspt');
			} else if($_GET['message'] == '4') { // Check delete message
				$msg = esc_html__("Status Changed Successfully.",'wwspt');
			}
		}
		
		// Displaying message
		if( !empty($msg) ) {
			$html = '<div class="updated" id="message">
						<p><strong>'.$msg.'</strong></p>
					</div>';
			echo $html;
		}
	?>
	
    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
    <form id="product-filter" method="get">
        
    	<!-- For plugins, we also need to ensure that the form posts back to our current page -->
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        
        <?php //$ProductListTable->views() ?>
        
        <!-- Search Title -->
        <?php $ProductListTable->search_box( esc_html__( 'Search', 'wwspt' ), 'ww_spt_search' ); ?>
        
        <!-- Now we can render the completed list table -->
        <?php $ProductListTable->display() ?>
        
    </form>
	
</div><!-- end .ww-spt-wrap -->
<!-- List Table Output Ends Here -->