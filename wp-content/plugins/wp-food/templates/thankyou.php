<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}
global $userfood, $billing, $id;

?>
<div class="exfood-thankyou">
  <div class="ex-tk-message">
    <h3><?php esc_html_e('Order received','wp-food');?></h3>
    <p><?php esc_html_e('Thank you. Your order has been received.','wp-food');?></p>
  </div>
  <div class="ex-order-info">
    <ul>
      <li>
        <span><?php esc_html_e('Order Number','wp-food');?></span>
        <strong>#<?php echo esc_html($id);?></strong>
      </li>
      <li>
        <span><?php esc_html_e('Order Type','wp-food');?></span>
        <strong><?php
          $order_type='';
          $order_type = $billing['_type'];
          if ($order_type == 'order-delivery') {
            echo esc_html_e('Order and  wait delivery','wp-food');
          }else{
            echo esc_html_e('Order and carryout','wp-food');
          }
        ?></strong>
      </li>
      <?php if($billing['_date']!=''){?>
      <li>
        <span><?php esc_html_e('Date Delivery','wp-food');?></span>
        <strong><?php echo date_i18n(get_option('date_format'),$billing['_date']);?></strong>
      </li>
      <?php }
      if($billing['_time']!=''){?>
      <li>
        <span><?php esc_html_e('Time Delivery','wp-food');?></span>
        <strong><?php echo esc_html($billing['_time']);?></strong>
      </li>
      <?php }
      if($billing['_phone']!=''){?>
      <li>
        <span><?php esc_html_e('Order Phone','wp-food');?></span>
        <strong><?php echo esc_html($billing['_phone']);?></strong>
      </li>
      <?php }
      if($billing['_email']!=''){?>
      <li>
        <span><?php esc_html_e('Order Email','wp-food');?></span>
        <strong><?php echo esc_html($billing['_email']);?></strong>
      </li>
      <?php }?>
    </ul>
  </div>
  <div class="ex-order-details">
    <h3><?php esc_html_e('Order details','wp-food');?></h3>
    <table>
      <thead>
        <tr>
          <th><?php esc_html_e('Image','wp-food');?></th>
          <th><?php esc_html_e('Name','wp-food');?></th>
          <th><?php esc_html_e('Quantity','wp-food');?></th>
          <th><?php esc_html_e('Total','wp-food');?></th>
        </tr>
      </thead>
      <tbody>
      <?php
      $total_price = 0;
      foreach ($userfood as $key => $value) {
        $food_id = $value['food_id'];
        $price_food = get_post_meta( $food_id, 'exfood_price', true );
        $saleprice = get_post_meta( $food_id, 'exfood_sale_price', true );
        $price_food = $saleprice!='' && is_numeric($saleprice) ? $saleprice : $price_food;
        $price_food = is_numeric($price_food) ? $price_food : 0;
        echo '<tr>
            <td class="exfood-cart-image">
                <img style="width:80px;" src="'.get_the_post_thumbnail_url($food_id,'exfood_80x80').'"/>
            </td>
            <td class="exfood-cart-details" >
              <p>'.get_the_title($food_id).'</p>';
              foreach ($value as $key_it => $item_meta) {
                if(is_array($item_meta)){
                  echo '<span class="exfood-addon">';
                  foreach ($item_meta as $val) {
                    $val = explode("|",$val);
                    $price = isset ($val[2]) ? $val[2] : '';
                    $price_food = $price!='' && is_numeric($price) ? $price_food + $price*1 : $price_food;
                    if($price!=''){
                      echo '<p>'.$val[1] .': '.exfood_price_with_currency($price).'</p>';
                    }else{
                      echo '<p>'.$val[1] .'</p>';
                    }
                  }
                  echo '</span>';
                }
              }
              $price_food = $price_food * $value['food_qty'];
              $total_price = $total_price + $price_food;
              echo '
            </td>
            <td class="exfood-cart-quatity">';
                echo '<span>'.$value['food_qty'].'</span>';
              echo '
            </td>
            <td class="exfood-cart-price">'.exfood_price_with_currency($price_food).'</td>';
            echo '
          </tr>';
        }
        ?>
        </tbody>  
    </table>
    <table>
      <tr>
        <th><?php esc_html_e('Payment method','wp-food');?></th>
        <td><?php esc_html_e('Cash on delivery','wp-food');?></td>
      </tr>
      <tr>
        <th><?php esc_html_e('Total','wp-food');?></th>
        <td><?php echo exfood_price_with_currency($total_price);?></td>
      </tr>
    </table>
  </div>
  <div class="ex-order-billing">
    <h3><?php esc_html_e('Order billing','wp-food');?></h3>
    <address>
      <?php
        $termad = get_term_by('slug', $billing['_location'], 'exfood_loc');
        $name = $termad->name;
        echo esc_html__('Name: ','wp-food').$billing['_fname'].' '.$billing['_lname'].'<br>';
        echo esc_html__('Location: ','wp-food').$name.'<br>';
        $order_store = $billing['_store'];
        if ($order_store !='') {
          echo esc_html__('Store: ','wp-food').get_the_title( $order_store ).'<br>';
        }
        echo $billing['_address'] !='' ? esc_html__('Address: ','wp-food').$billing['_address'].'<br>' : '';
        echo $billing['_phone']!='' ? esc_html__('Phone: ','wp-food').$billing['_phone'].'<br>' : '';
        echo $billing['_email']!='' ? esc_html__('Email: ','wp-food').$billing['_email'].'<br>' : '';
        echo $billing['_note']!='' ? esc_html__('Note :','wp-food').$billing['_note'].'<br>' : '';
      ?>
    </address>
  </div>
</div>