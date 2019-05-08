<?php
include 'class-wp-food-postype.php';
include 'shortcode-builder.php';
if ( exfood_get_option('exfood_booking') !='woo' ) {
	include 'class-wp-order-postype.php';
}
add_action( 'admin_enqueue_scripts', 'exfood_admin_scripts' );
function exfood_admin_scripts(){
	$js_params = array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) );
	wp_localize_script( 'jquery', 'exfood_ajax', $js_params  );
	wp_enqueue_style('ex-admin_style', EXFOOD_PATH . 'admin/css/style.css','','1.0');
	wp_enqueue_script('ex-admin-js', EXFOOD_PATH . 'admin/js/admin.js', array( 'jquery' ),'1.0' );
}

add_filter( 'manage_ex_food_posts_columns', 'exfood_edit_columns',99 );
function exfood_edit_columns( $columns ) {
	global $wpdb;
	unset($columns['date']);
	$columns['exfood_id'] = esc_html__( 'ID' , 'wp-food' );
	$columns['date'] = esc_html__( 'Publish date' , 'wp-food' );		
	return $columns;
}
add_action( 'manage_ex_food_posts_custom_column', 'ex_food_custom_columns',12);
function ex_food_custom_columns( $column ) {
	global $post;
	switch ( $column ) {
		case 'exfood_id':
			$exfood_id = $post->ID;
			echo '<span class="exfood_id">'.wp_kses_post($exfood_id).'</span>';
			break;
	}
}


add_filter( 'manage_exfood_scbd_posts_columns', 'exfood_edit_scbd_columns',99 );
function exfood_edit_scbd_columns( $columns ) {
	global $wpdb;
	unset($columns['date']);
	$columns['layout'] = esc_html__( 'Type' , 'wp-food' );
	$columns['shortcode'] = esc_html__( 'Shortcode' , 'wp-food' );
	$columns['date'] = esc_html__( 'Publish date' , 'wp-food' );		
	return $columns;
}
add_action( 'manage_exfood_scbd_posts_custom_column', 'exfood_scbd_custom_columns',12);
function exfood_scbd_custom_columns( $column ) {
	global $post;
	switch ( $column ) {
		case 'layout':
			$sc_type = get_post_meta($post->ID, 'sc_type', true);
			$exfood_id = $post->ID;
			echo '<span class="layout">'.wp_kses_post($sc_type).'</span>';
			break;
		case 'shortcode':
			$_shortcode = get_post_meta($post->ID, '_shortcode', true);
			echo '<input type="text" readonly name="_shortcode" value="'.esc_attr($_shortcode).'">';
			break;	
	}
}

add_action( 'wp_ajax_exfood_change_sort_mb', 'exfood_change_sort' );
function exfood_change_sort(){
	$post_id = $_POST['post_id'];
	$value = $_POST['value'];
	if(isset($post_id) && $post_id != 0)
	{
		update_post_meta($post_id, 'exfood_order', esc_attr(str_replace(' ', '', $value)));
	}
	die;
}
function exfood_id_taxonomy_columns( $columns ){
	$columns['cat_id'] = esc_html__('ID','wp-food');

	return $columns;
}
add_filter('manage_edit-exfood_cat_columns' , 'exfood_id_taxonomy_columns');
function exfood_taxonomy_columns_content( $content, $column_name, $term_id ){
    if ( 'cat_id' == $column_name ) {
        $content = $term_id;
    }
	return $content;
}
add_filter( 'manage_exfood_cat_custom_column', 'exfood_taxonomy_columns_content', 10, 3 );

// order item table
function exfood_table_prder_item(){
	echo '<div class="exfd-order-items">';
		echo exfood_table_order_item_html();
	echo '</div>';
}
// add new order metadata html
function exfood_add_order_item_meta_html($key){
	$id = 'exfd-order-'.rand(10,9999);
	$output = '
	<div class="exfood-add-order-item-meta">
	<span id="'.esc_attr($id).'" class="button add-order-meta" data-pl="'.esc_html__('Name | Price','wp-food').'">'.esc_html__('+ Add meta','wp-food').'</span>
	<span class="button button-primary save-order-meta" data-update="'.esc_attr($key).'">'.esc_html__('Save','wp-food').'</span>
	</div>
	';
	ob_start();
	$js_string = ob_get_contents();
	ob_end_clean();
	return $output.$js_string;
}
function exfood_table_order_item_html(){
	$post_id = isset($_POST['post_id']) && $_POST['post_id']!='' ? $_POST['post_id'] : get_the_ID() ;
	$userfood = get_post_meta( $post_id, 'exorder_food', true);
	if(!is_array($userfood) || empty($userfood)){
		return;
	}
	$html = '
	<table>
	  <tr class="exfood-cart-header">
	    <td class="exfood-cart-image"></td>
	    <td class="exfood-cart-details">'.esc_html__( 'Details', 'wp-food' ).'</td>
	    <td class="exfood-cart-quatity exfood-quantity">'.esc_html__( 'Quantity', 'wp-food' ).'</td>
	    <td class="exfood-cart-price">'.esc_html__( 'Total', 'wp-food' ).'</td>
	    <td class="exfood-cart-close"></td>';
	    $html .= '
	  </tr>';
	  $total_price = 0;
	  foreach ($userfood as $key => $value) {
	    $food_id = $value['food_id'];
	    $price_food = get_post_meta( $food_id, 'exfood_price', true );
	    $saleprice = get_post_meta( $food_id, 'exfood_sale_price', true );
	    $price_food = $saleprice!='' && is_numeric($saleprice) ? $saleprice : $price_food;
	    $price_food = is_numeric($price_food) ? $price_food : 0;
	    $customlink = get_edit_post_link($food_id);
	    $html .= '
	    <tr>
			<td class="exfood-cart-image">
			<a class="exfood-title" href="'.esc_url($customlink).'">
			  <img src="'.get_the_post_thumbnail_url($food_id,'exfood_80x80').'"/>
			</a>
			</td>
			<td class="exfood-cart-details">
			<a class="exfood-title" href="'.esc_url($customlink).'">'.get_the_title($food_id).'</a>';
			foreach ($value as $key_it => $item_meta) {
			  if(is_array($item_meta)){
			    $html .= '<span class="exfood-addon">';
			    foreach ($item_meta as $val) {
			      $val = explode("|",$val);
			      $price = isset ($val[2]) ? $val[2] : '';
			      $price_food = $price!='' && is_numeric($price) ? $price_food + $price*1 : $price_food;
			      if($price!=''){
			        $html .= '<p>'.wp_kses_post($val[1]) .': '.exfood_price_with_currency($price).'</p>';
			      }else{
			        $html .= '<p>'.wp_kses_post($val[1]) .'</p>';
			      }
			    }
			    $html .= '</span>';
			  }
			}
			$price_food = $price_food * $value['food_qty'];
			$total_price = $total_price + $price_food;
			$html .= exfood_add_order_item_meta_html($key);
			$html .= '
			</td>
			<td class="exfood-cart-quatity exfood-quantity">';
				if(isset($checkout) && $checkout ==1){
				  $html .= '<span>'.wp_kses_post($value['food_qty']).'</span>';
				}else{
				  $html .= '
				  <input type="number" min="1" name="food_qty" class="food_qty" value="'.esc_attr($value['food_qty']).'" data-update="'.esc_attr($key).'">
				  ';
				}
			$html .= '
			</td>
			<td class="exfood-cart-price">'.exfood_price_with_currency($price_food).'</td>
			<td class="exfood-cart-close">
			    <a class="exfood-close" href="javascript:;" data-remove="'.esc_attr($key).'">×</a>
			</td>';
	      $html .= '
	    </tr>';
	  }
	$html .= '</table>';
	$html .= '
	<p class="exfood-total"><strong>'.esc_html__('Subtotal:','wp-food').'</strong>
	  <span>'.exfood_price_with_currency($total_price).'</span>
	</p>';
	return $html;
}
add_action( 'wp_ajax_exfood_admin_add_order_item', 'exfood_admin_add_order_item' );
function exfood_admin_add_order_item(){
	$post_id = $_POST['post_id'];
	$food_id = $_POST['food_id'];
	if($food_id > 0 && $post_id > 0){
		$userfood = get_post_meta( $post_id, 'exorder_food', true);
		if (!is_array($userfood)) {
			$userfood = array();
		}
		$userfood[] = array(
			'food_id' => $food_id,
			'food_qty' => 1,
		);
		update_post_meta($post_id, 'exorder_food', $userfood);
		echo exfood_table_order_item_html();
	}
	die;
}

add_action( 'wp_ajax_adm_exfood_remove_cart_item', 'adm_exfood_remove_cart_item' );
function adm_exfood_remove_cart_item(){
	$key = $_POST['it_remove'];
	$post_id = $_POST['post_id'];
	if(is_numeric($key)){
		$userfood = get_post_meta( $post_id, 'exorder_food', true);
		unset($userfood[$key]);
		update_post_meta($post_id, 'exorder_food', $userfood);
		$avari = 1;
	}else{
		$avari = 0;
	}
	$total_price = exfood_update_total_price($userfood);
	$output =  array('status'=>$avari,'update_total'=> $total_price);
	echo str_replace('\/', '/', json_encode($output));
	exit;	
}

add_action( 'wp_ajax_adm_exfood_update_cart_item', 'adm_exfood_update_cart_item' );
add_action( 'wp_ajax_nopriv_adm_exfood_update_cart_item', 'adm_exfood_update_cart_item' );

function adm_exfood_update_cart_item(){
	$key = $_POST['it_update'];
	$qty = $_POST['qty'];
	$post_id = $_POST['post_id'];
	$userfood = get_post_meta( $post_id, 'exorder_food', true);
	if(is_numeric($key) && isset($userfood[$key])){
		$userfood[$key]['food_qty'] = $qty;
		update_post_meta($post_id, 'exorder_food', $userfood);
		$avari = 1;
	}else{
		$avari = 0;
		$number_item = count($userfood);
		$output =  array('status'=>$avari,'info_text'=> esc_html__("This item does not exist in cart","wp-food"),'number_item'=>$number_item);
		echo str_replace('\/', '/', json_encode($output));
		exit;
	}
	$total_price = exfood_update_total_price($userfood);
	$food_id = $userfood[$key]['food_id'];
	$price_food = get_post_meta( $food_id, 'exfood_price', true );
	$saleprice = get_post_meta( $food_id, 'exfood_sale_price', true );
	$price_food = $saleprice!='' && is_numeric($saleprice) ? $saleprice : $price_food;
	$price_food = is_numeric($price_food) ? $price_food : 0;
	foreach ($userfood[$key] as $key_it => $item_meta) {
		if(is_array($item_meta)){
			foreach ($item_meta as $val) {
				$val = explode("|",$val);
				$price = isset ($val[2]) ? $val[2] : 0;
				$price_food = $price_food + $price*1;
			}
		}
	}
	$total_price = $price_food * $userfood[$key]['food_qty'];
	$total_cart = exfood_update_total_price($userfood);
	$output =  array('status'=>$avari,'update_price'=> exfood_price_with_currency($total_price),'update_total'=> $total_cart);
	echo str_replace('\/', '/', json_encode($output));
	exit;	
}


add_action( 'wp_ajax_adm_exfood_add_order_meta', 'adm_exfood_add_order_meta' );
function adm_exfood_add_order_meta(){
	$key = $_POST['it_update'];
	$post_id = $_POST['post_id'];
	$metas = $_POST['metas'];
	if(empty($metas)){
		echo 0; exit;
	}
	if(is_numeric($key)){
		$userfood = get_post_meta( $post_id, 'exorder_food', true);
		$html ='';
		foreach ($metas as $value) {
			$val = '|'.$value;
			$userfood[$key]['ex_extrafood_adm'][] = $val;
			$val = explode("|",$val);
			$price = isset ($val[2]) ? $val[2] : '';
			if($price!=''){
				$html .= '<p>'.$val[1] .': '.exfood_price_with_currency($price).'</p>';
			}else{
				$html .= '<p>'.$val[1] .'</p>';
			}
		}
		update_post_meta($post_id, 'exorder_food', $userfood);
		$avari = 1;
	}else{
		$avari = 0;
	}
	$total_price = exfood_update_total_price($userfood);
	$food_id = $userfood[$key]['food_id'];
	$price_food = get_post_meta( $food_id, 'exfood_price', true );
	$saleprice = get_post_meta( $food_id, 'exfood_sale_price', true );
	$price_food = $saleprice!='' && is_numeric($saleprice) ? $saleprice : $price_food;
	$price_food = is_numeric($price_food) ? $price_food : 0;
	foreach ($userfood[$key] as $key_it => $item_meta) {
		if(is_array($item_meta)){
			foreach ($item_meta as $val) {
				$val = explode("|",$val);
				$price = isset ($val[2]) ? $val[2] : 0;
				$price_food = $price_food + $price*1;
			}
		}
	}
	$total_item = $price_food * $userfood[$key]['food_qty'];
	$output =  array('status'=>$avari,'update_total'=> $total_price, 'update_price'=> exfood_price_with_currency($total_item), 'html_add'=> '<span class="exfood-addon">'.$html.'</div>');
	echo str_replace('\/', '/', json_encode($output));
	exit;	
}

add_action( 'wp_ajax_exfood_admin_show_store', 'exfood_admin_show_store' );
function exfood_admin_show_store(){
	$store_id = $_POST['store_id'];
	if(!is_numeric($store_id)){
		return;
	}
	$store_name = '';
	$store_name = get_the_title( $store_id );
	$output =  array('store_name'=>$store_name);
	echo str_replace('\/', '/', json_encode($output));
	exit;	
}