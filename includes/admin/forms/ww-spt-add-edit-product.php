<?php 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Add/Edit product
 *
 * Handle Add / Edit product
 * 
 * @package WP List Table (WP Table)
 * @since 1.0.0
 */

	global $ww_spt_model, $errmsg,$error; //make global for error message to showing errors
		
	$prefix = WW_SPT_META_PREFIX;
	$model = $ww_spt_model;
	
	//set default value as blank for all fields
	//preventing notice and warnings
	$data = array( 
				'ww_spt_product_title'		=> '',
				'ww_spt_product_desc' 		=> '',
				//'ww_spt_product_status' 		=> '', 
				'ww_spt_product_avail' 		=> array(), 
				'ww_spt_featured_product'	=> '0',
				'ww_spt_product_price'		=>	''
			);
	
	if(isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['spt_id']) && !empty($_GET['spt_id'])) { //check action & id is set or not
		
		//product page title
		$product_lable = esc_html__('Edit Product', 'wwspt');
		
		//product page submit button text either it is Add or Update
		$product_btn = esc_html__('Update', 'wwspt');
		
		//get the product id from url to update the data and get the data of product to fill in editable fields
		$post_id = $_GET['spt_id'];
		
		//get the data from product id
		$getpost = get_post( $post_id );
		
		//assign retrived data to current page fields data to show filled in fields
		if($error != true) { //if error is not occured then fill with database values
			$data['ww_spt_product_title'] = $getpost->post_title;
			$data['ww_spt_product_desc'] = $getpost->post_content;
			//$data['ww_spt_product_status'] = get_post_meta($post_id,$prefix.'product_status',true);
			$data['ww_spt_product_avail'] = get_post_meta($post_id,$prefix.'product_avail',true);
			$data['ww_spt_featured_product'] = get_post_meta($post_id,$prefix.'featured_product',true);
			$data['ww_spt_product_price'] = get_post_meta($post_id,$prefix.'product_price',true);
		} else {
			$data = $_POST;
		}
		
	} else {
		
		//product page title
		$product_lable = esc_html__('Add New Product', 'wwspt');
		
		//product page submit button text either it is Add or Update
		$product_btn = esc_html__('Save', 'wwspt');
		
		//if when error occured then assign $_POST to be field fields with none error fields
		if($_POST) { //check if $_POST is set then set all $_POST values
			$data = $_POST;
		}
	}
		
	//when product availablity array is null then after submitting then assign with blank array
	if (empty($data['ww_spt_product_avail'])) { //check if product avail is empty
		$data['ww_spt_product_avail'] = array();
	}
?>

	<div class="wrap">
		
		<h2><?php echo esc_html__( $product_lable , 'wwspt') ?>
			<a class="add-new-h2" href="admin.php?page=ww_spt_products"><?php echo esc_html__('Back to List','wwspt') ?></a>
		</h2>	
	
	<!-- beginning of the product meta box -->

		<div id="ww-spt-product" class="post-box-container">
		
			<div class="metabox-holder">	
		
				<div class="meta-box-sortables ui-sortable">
		
					<div id="product" class="postbox">	
		
						<div class="handlediv" title="<?php echo esc_html__( 'Click to toggle', 'wwspt' ) ?>"><br />
						</div>
		
						<!-- product box title -->
	
						<h3 class="hndle">		
							<span style="vertical-align: top;"><?php echo esc_html__( $product_lable, 'wwspt' ) ?></span>		
						</h3>
	
						<div class="inside">
						
							<form action="" method="POST" id="ww-spt-add-edit-form">
								<input type="hidden" name="page" value="ww_spt_add_products" />										
								<div id="ww-spt-require-message">
									<strong>(</strong><span class="ww-spt-require">*</span><strong>)<?php echo esc_html__( 'Required fields', 'wwspt' ) ?></strong>
								</div>
								<table class="form-table ww-spt-product-box"> 
									<tbody>							
										
										<tr>
											<th scope="row">
												<label><strong><?php echo esc_html__( 'Title:', 'wwspt' ) ?></strong><span class="ww-spt-require"> * </span></label>
											</th>
											<td><input type="text" id="ww_spt_product_title" name="ww_spt_product_title" value="<?php echo $model->ww_spt_escape_attr($data['ww_spt_product_title']) ?>" class="large-text"/><br />
												<span class="description"><?php echo esc_html__( 'Enter the product title.', 'wwspt' ) ?></span>
											</td>
											<td class="ww-spt-product-error">
												<?php
												if(isset($errmsg['product_title']) && !empty($errmsg['product_title'])) { //check error message for product title
													echo '<div>'.$errmsg['product_title'].'</div>';
												}
												?>										
											</td>
										</tr>
								
										<tr>
											<th scope="row">
												<label><strong><?php echo esc_html__( 'Description:', 'wwspt' ) ?></strong><span class="ww-spt-require"> * </span></label>
											</th>
											<td  width="35%"><textarea id="ww_spt_product_desc" name="ww_spt_product_desc" rows="4" class="large-text"><?php echo $model->ww_spt_escape_attr($data['ww_spt_product_desc']) ?></textarea><br />
												<span class="description"><?php echo esc_html__( 'Enter the product description.', 'wwspt' ) ?></span>
											</td>
											<td class="ww-spt-product-error">
												<?php
												if(isset($errmsg['product_desc']) && !empty($errmsg['product_desc'])) { //check error message for product content
													echo '<div>'.$errmsg['product_desc'].'</div>';
												}
												?>										
											</td>
										</tr>
									
										<tr>
											<th scope="row">
												<label><strong><?php echo esc_html__( 'Availability:', 'wwspt' ) ?></strong></label>
											</th>
											<td class="ww-spt-avail-chk" width="35%">
												<input type="checkbox" name="ww_spt_product_avail[]" value="Client"<?php echo checked( in_array('Client',$data['ww_spt_product_avail']), true, false ) ?>/><label><?php echo esc_html__( ' Client', 'wwspt' ) ?></label>
												<input type="checkbox" name="ww_spt_product_avail[]" value="Distributor"<?php echo checked( in_array('Distributor',$data['ww_spt_product_avail']), true, false ) ?>/><label><?php echo esc_html__( ' Distributor', 'wwspt' ) ?></label>
												<br /><span class="description"><?php echo esc_html__( 'Choose the product availability.', 'wwspt' ) ?></span>
											</td>
										</tr>
								
										<tr>
											<th scope="row">
												<label><strong><?php echo esc_html__( 'Featured product:', 'wwspt' ) ?></strong></label>
											</th>
											<td width="35%">
												<input type="radio" id="ww_spt_featured_product" name="ww_spt_featured_product" value="1"<?php echo checked($data['ww_spt_featured_product'],'1',false) ?>/><?php echo esc_html__('Yes','wwspt') ?>
												<input type="radio" id="ww_spt_featured_product" name="ww_spt_featured_product" value="0"<?php echo checked($data['ww_spt_featured_product'],'0',false) ?>/><?php echo esc_html__('No','wwspt') ?>
												<br /><span class="description"><?php echo esc_html__( 'Enter the featured product.', 'wwspt' ) ?></span>
											</td>
										</tr>
								
										<tr>
											<th scope="row">
												<label><strong><?php echo esc_html__( 'Price:', 'wwspt' ) ?></strong></label>
											</th>
											<td><input type="text" id="ww_spt_product_price" name="ww_spt_product_price" value="<?php echo $model->ww_spt_escape_attr($data['ww_spt_product_price']) ?>" class="large-text"/><br />
												<span class="description"><?php echo esc_html__( 'Enter the product price.', 'wwspt' ) ?></span>
											</td>
										</tr>
								
										<tr>
											<td colspan="3">
												<input type="submit" class="button-primary margin_button" name="ww_spt_product_save" id="ww_spt_product_save" value="<?php echo $product_btn ?>"/>
											</td>
										</tr>
										
									</tbody>
								</table>
							</form>
						
						</div><!-- .inside -->
			
					</div><!-- #product -->
		
				</div><!-- .meta-box-sortables ui-sortable -->
		
			</div><!-- .metabox-holder -->
		
		</div><!-- #wps-product-general -->
		
	<!-- end of the product meta box -->
	
	</div><!-- .wrap -->