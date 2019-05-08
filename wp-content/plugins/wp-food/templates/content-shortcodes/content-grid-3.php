<?php
  $customlink = EX_WPFood_customlink(get_the_ID());
  global $number_excerpt,$img_size;
  if($img_size==''){$img_size = 'exfood_400x400';}
  $price = get_post_meta( get_the_ID(), 'exfood_price', true );
  $saleprice = get_post_meta( get_the_ID(), 'exfood_sale_price', true );
  $currency = exfood_get_option('exfood_currency');
  $num_decimal = exfood_get_option('exfood_num_decimal');
  $decimal_sep = exfood_get_option('exfood_decimal_sep');
  if ($currency=='') {
    $currency ='$';
  }
  $position = exfood_get_option('exfood_position');
  if ($num_decimal > 0) {
    $price = exfood_price_with_currency($price);
    if ($saleprice > 0){
      $saleprice = exfood_price_with_currency($saleprice);
    }
  }
?>
<figure class="exstyle-3 tppost-<?php the_ID();?> <?php if($number_excerpt !='0'){ echo "exstyle-3-center"; }?>">
  <div class="exstyle-3-image ex-fly-cart" style="background-image: url(<?php echo get_the_post_thumbnail_url(get_the_ID(),$img_size); ?>)">
    <a class="exfd_modal_click" href="<?php echo esc_url($customlink); ?>"></a>
    <?php exfood_sale_badge($saleprice); 
    if(exfood_get_option('exfood_booking') !='woo'){
      exfood_booking_button_html(3);
    }else{?>
      <div class="exbt-inline">
        <a href="<?php echo esc_url($customlink); ?>" class="exstyle-3-button"><?php esc_html_e( 'Order', 'wp-food' ) ?></a>
      </div>
    <?php }?>
  </div><figcaption>
    <h3><a class="exfd_modal_click" href="<?php echo esc_url($customlink); ?>"><?php the_title(); ?></a></h3>
    <h5>
      <?php if ($saleprice > 0) {?>
        <del><?php echo wp_kses_post($price); ?></del> <ins><?php echo wp_kses_post($saleprice); ?></ins>
      <?php }else{
        echo wp_kses_post($price);
      } ?>
    </h5>
    <?php 
    if(has_excerpt(get_the_ID())){?>
      <p><?php echo wp_trim_words(get_the_excerpt(),$number_excerpt,'...'); ?></p>
    <?php }
    ?>
  </figcaption>
</figure>