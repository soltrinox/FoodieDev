<?php if ( exfood_get_option('exfood_booking') !='woo' ) {
$userfood = isset($_SESSION['ex_userfood']) && !empty($_SESSION['ex_userfood']) ? $_SESSION['ex_userfood'] : array();
if(empty($userfood)){
  echo
  '<div class="exfood-warning">'.esc_html__('Your cart is currently empty.','wp-food').'</div>';
  return;
} 
echo '<div class="exfd-cart-buildin">
<input type="hidden"  name="ajax_url" value="'.esc_url(admin_url( 'admin-ajax.php' )).'">
<ul>';
  $userfood = isset($_SESSION['ex_userfood']) && !empty($_SESSION['ex_userfood']) ? $_SESSION['ex_userfood'] : array();
  $total_price = 0;
  foreach ($userfood as $key => $value) {
    $food_id = $value['food_id'];
    $price_food = get_post_meta( $food_id, 'exfood_price', true );
    $saleprice = get_post_meta( $food_id, 'exfood_sale_price', true );
    $price_food = $saleprice!='' && is_numeric($saleprice) ? $saleprice : $price_food;
    $price_food = is_numeric($price_food) ? $price_food : 0;
    $customlink = EX_WPFood_customlink($food_id);
    echo '<li>
      <a class="exfood-close" href="javascript:;" data-remove="'.esc_attr($key).'">×</a>
      <a class="exfood-title" href="'.esc_url($customlink).'">
      <img src="'.get_the_post_thumbnail_url($food_id,'exfood_80x80').'"/>
      '.get_the_title($food_id).'</a>';
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
      $total_price = $total_price + $price_food * $value['food_qty'];
      echo '
      <span class="exfood-quantity">'.wp_kses_post($value['food_qty']).' × '.exfood_price_with_currency($price_food).'</span>
    </li>';
  }
echo '</ul>';
$checkout_page = exfood_get_option('exfood_checkout_page','exfood_advanced_options');
$cart_page = exfood_get_option('exfood_cart_page','exfood_advanced_options');
echo '<p class="exfood-total"><strong>'.esc_html__('Subtotal:','wp-food').'</strong>
  <span>'.exfood_price_with_currency($total_price).'</span>
  </p>
  <p class="woocommerce-mini-cart__buttons buttons">
  <a href="'.esc_url(get_the_permalink($cart_page)).'">'.esc_html__('View cart','wp-food').'</a>
  <a href="'.esc_url(get_the_permalink($checkout_page)).'">'.esc_html__('Checkout','wp-food').'</a>
  </p>
</div>';
}else{
echo '<div class="exfd-cart-mini">';woocommerce_mini_cart();echo '</div>';
}?>