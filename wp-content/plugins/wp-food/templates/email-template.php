<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}
global $userfood,$id,$hd_title,$details,$billing;
$style = 'style="text-align:left;color:#737373;border:1px solid #e4e4e4;padding:12px"';
$exfood_color = exfood_get_option('exfood_color')!='' ? exfood_get_option('exfood_color') : '#ea1f2e';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />
    <title><?php echo get_bloginfo( 'name', 'display' ); ?></title>
  </head>
  <body <?php echo is_rtl() ? 'rightmargin' : 'leftmargin'; ?>="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
    <div id="wrapper" dir="<?php echo is_rtl() ? 'rtl' : 'ltr'?>">
      <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" style="background-color: #fdfdfd">
        <tr>
          <td align="center" valign="top">
            
            <table border="0" cellpadding="0" cellspacing="0" width="600" id="template_container">
              <tr>
                <td align="center" valign="top">
                  <!-- Header -->
                  <table border="0" cellpadding="0" cellspacing="0" width="600" id="template_header">
                    <tr>
                      <td id="header_wrapper" style="border:1px solid #e4e4e4; color:#fff;background-color: <?php echo esc_attr( $exfood_color )?>; border-radius: 3px 3px 0 0!important; padding: 15px 25px;" cellpadding="20" cellspacing="0" width="100%;">
                        <h1><?php echo $hd_title; ?></h1>
                      </td>
                    </tr>
                  </table>
                  <!-- End Header -->
                </td>
              </tr>
              <tr>
                <td align="center" valign="top" style="padding: 15px;">
                  <!-- Body -->
                  <table border="0" cellpadding="0" cellspacing="0" width="600" id="template_body">
                    <tr>
                      <td>
                        <div style="text-align: left;"><?php echo $details;?></div>
                        <h2 style="font-size: 18px; font-weight: bold;line-height: 90%;">
                          <?php echo esc_html__('Order: #','wp-food') .$id. ' ('.get_the_date( $d = '', $id ).')';?>
                        </h2>
                        <h5 style="font-size: 13px; line-height: 90%;font-weight: 500;">
                          <?php $order_type='';
                          echo esc_html_e('Order Type: ','wp-food');
                          $order_type = $billing['_type'];
                          if ($order_type == 'order-delivery') {
                            echo esc_html_e('Order and  wait delivery','wp-food');
                          }else{
                            echo esc_html_e('Order and carryout','wp-food');
                          }?>
                        </h5>
                        <h5 style="font-size: 13px; line-height: 90%;font-weight: 500;">
                          <?php echo esc_html_e('Date Delivery','wp-food').'('.esc_html(date_i18n(get_option('date_format'), $billing['_date'])).'), ';
                          echo esc_html_e('Time Delivery','wp-food').' ('.esc_html($billing['_time']).')';?>
                        </h5>
                      </td>
                    </tr>
                    <tr>
                      <td valign="top" id="body_content">
                        <!-- Content -->
                        <table style="border:1px solid #e4e4e4;" cellpadding="20" cellspacing="0" width="100%">
                          <thead>
                            <tr>
                              <th valign="top" <?php echo $style;?> ><?php esc_html_e('Image','wp-food');?></th>
                              <th valign="top" <?php echo $style;?> ><?php esc_html_e('Name','wp-food');?></th>
                              <th valign="top" <?php echo $style;?> ><?php esc_html_e('Quantity','wp-food');?></th>
                              <th valign="top" <?php echo $style;?> ><?php esc_html_e('Total','wp-food');?></th>
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
                                <td class="exfood-cart-image" '.$style.'>
                                    <img style="width:80px;" src="'.get_the_post_thumbnail_url($food_id,'exfood_80x80').'"/>
                                </td>
                                <td class="exfood-cart-details" '.$style.' >
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
                                <td class="exfood-cart-quatity exfood-quantity" '.$style.'>';
                                    echo '<span>'.$value['food_qty'].'</span>';
                                  echo '
                                </td>
                                <td class="exfood-cart-price" '.$style.'>'.exfood_price_with_currency($price_food).'</td>';
                                echo '
                              </tr>';
                            }
                            ?>
                            </tbody>
                            <tfoot>
                              <tr>
                                <th <?php echo $style;?> colspan="3"><?php esc_html_e('Total','wp-food');?></th>
                                <td <?php echo $style;?>><?php echo exfood_price_with_currency($total_price);?></td>
                              </tr>
                            </tfoot>
                        </table>
                        <!-- End Content -->
                      </td>
                    </tr>
                    <tr><td style="padding: 10px"></td></tr>
                    <tr><td><h2 style="font-size: 18px; font-weight: bold;line-height: 90%;"><?php echo esc_html__('Billing address','wp-food'); ?></h2></td></tr>
                    <tr>
                      <td style="text-align:left;color:#737373;border:2px solid #e4e4e4;padding:12px">
                        <address>
                          <?php 
                            echo esc_html__('Name: ','wp-food').$billing['_fname'].' '.$billing['_lname'].'<br>';
                            $termad = get_term_by('slug', $billing['_location'], 'exfood_loc');
                            $name = $termad->name;
                            echo esc_html__('Location: ','wp-food').$name.'<br>';
                            $order_store = $billing['_store'];
                            if ($order_store !='') {
                              echo esc_html__('Store: ','wp-food').get_the_title( $order_store ).'<br>';
                            }
                            echo esc_html__('Address: ','wp-food').$billing['_address'].'<br>';
                            echo esc_html__('Phone: ','wp-food').$billing['_phone'].'<br>';
                            echo esc_html__('Email: ','wp-food').$billing['_email'].'<br>';
                            echo esc_html__('Note :','wp-food').$billing['_note'].'<br>';
                          ?>
                        </address>
                      </td>
                    </tr>
                  </table>
                  <!-- End Body -->
                </td>
              </tr>
              <tr>
                <td align="center" valign="top">
                  <!-- Footer -->
                  <table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer">
                    <tr>
                      <td valign="top">
                        <table border="0" cellpadding="10" cellspacing="0" width="100%">
                          <tr>
                            <td colspan="2" valign="middle" id="credit">
                              <?php  ?>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                  <!-- End Footer -->
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </div>
  </body>
</html>
