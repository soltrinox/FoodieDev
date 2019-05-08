<?php 
global $checkout;
$userfood = isset($_SESSION['ex_userfood']) && !empty($_SESSION['ex_userfood']) ? $_SESSION['ex_userfood'] : array();
if(empty($userfood)){
  echo
  '<div class="exfood-warning">'.esc_html__('Your cart is currently empty.','wp-food').'</div>';
  return;
}
if ( exfood_get_option('exfood_booking') !='woo' ) { 
if(!isset($checkout) || $checkout !=1){
  echo '
  <div class="exfood-mulit-steps">
    <div class="exfood-cart-step active">'.esc_html__('Your cart','wp-food').'</div>
    <div class="exfood-checount-step">'.esc_html__('Checkout','wp-food').'</div>
    <!-- <div class="exfood-confirm-step">'.esc_html__('Confirm your order','wp-food').'</div> -->
  </div>';
}
echo '
<div class="exfd-cart-buildin">
<input type="hidden"  name="ajax_url" value="'.esc_url(admin_url( 'admin-ajax.php' )).'">
<ul>
  <li class="exfood-cart-header">
    <div class="exfood-cart-image"></div>
    <div class="exfood-cart-details">'.esc_html__( 'Details', 'wp-food' ).'</div>
    <div class="exfood-cart-quatity exfood-quantity">'.esc_html__( 'Quantity', 'wp-food' ).'</div>
    <div class="exfood-cart-price">'.esc_html__( 'Total', 'wp-food' ).'</div>';
    if(isset($checkout) && $checkout ==1){}else{
      echo'
      <div class="exfood-cart-close"></div>';
    }
    echo '
  </li>';
  $total_price = 0;
  foreach ($userfood as $key => $value) {
    $food_id = $value['food_id'];
    $price_food = get_post_meta( $food_id, 'exfood_price', true );
    $saleprice = get_post_meta( $food_id, 'exfood_sale_price', true );
    $price_food = $saleprice!='' && is_numeric($saleprice) ? $saleprice : $price_food;
    $price_food = is_numeric($price_food) ? $price_food : 0;
    $customlink = EX_WPFood_customlink($food_id);
    echo '<li>
      <div class="exfood-cart-image">
        <a class="exfood-title" href="'.esc_url($customlink).'">
          <img src="'.get_the_post_thumbnail_url($food_id,'exfood_80x80').'"/>
        </a>
      </div>
      <div class="exfood-cart-details">
        <a class="exfood-title" href="'.esc_url($customlink).'">'.get_the_title($food_id).'</a>';
        foreach ($value as $key_it => $item_meta) {
          if(is_array($item_meta)){
            echo '<span class="exfood-addon">';
            foreach ($item_meta as $val) {
              $val = explode("|",$val);
              $price = isset ($val[2]) ? $val[2] : '';
              $price_food = $price!='' && is_numeric($price) ? $price_food + $price*1 : $price_food;
              if($price!=''){
                echo '<p>'.wp_kses_post($val[1]) .': '.exfood_price_with_currency($price).'</p>';
              }else{
                echo '<p>'.wp_kses_post($val[1]) .'</p>';
              }
            }
            echo '</span>';
          }
        }
        $price_food = $price_food * $value['food_qty'];
        $total_price = $total_price + $price_food;

        echo '
      </div>
      <div class="exfood-cart-quatity exfood-quantity">';
        if(isset($checkout) && $checkout ==1){
          echo '<span>'.wp_kses_post($value['food_qty']).'</span>';
        }else{
          echo '
          <input type="button" class="minus_food" value="-" data-update="'.esc_attr($key).'">
          <input type="number" min="1" name="food_qty" class="food_qty" value="'.esc_attr($value['food_qty']).'">
          <input type="button" class="plus_food" value="+" data-update="'.esc_attr($key).'">
          ';
        }
        echo '
      </div>
      <div class="exfood-cart-price">'.exfood_price_with_currency($price_food).'</div>';
      if(isset($checkout) && $checkout ==1){}else{
        echo '
        <div class="exfood-cart-close">
            <a class="exfood-close" href="javascript:;" data-remove="'.esc_attr($key).'">×</a>
        </div>';
      }
      echo '
    </li>';
  }
echo '</ul>';
echo '<p class="exfood-total"><strong>'.esc_html__('Subtotal:','wp-food').'</strong>
  <span>'.exfood_price_with_currency($total_price).'</span>
  </p>';
  if(isset($checkout) && $checkout ==1){}else{
    $checkout_page = exfood_get_option('exfood_checkout_page','exfood_advanced_options');
    echo '
    <p class="woocommerce-mini-cart__buttons buttons">
      <a href="'.esc_url(get_the_permalink($checkout_page)).'">'.esc_html__('Checkout','wp-food').'</a>
    </p>';
  }
  echo '
</div>';
}else{
echo '<div class="exfd-cart-mini">';woocommerce_mini_cart();echo '</div>';
}?>