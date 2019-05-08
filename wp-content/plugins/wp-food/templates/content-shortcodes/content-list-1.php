<?php
  $customlink = EX_WPFood_customlink(get_the_ID());
  global $number_excerpt;
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
  $class_add = '';
  if(!has_excerpt(get_the_ID())){
      $class_add = " ex-no-description";
  }

?>
<figure class="fdstyle-list-1 <?php echo esc_attr($class_add);?>">
  <?php if(has_post_thumbnail(get_the_ID())){ ?>
    <div class="exf-img"><?php the_post_thumbnail('exfood_80x80'); ?></div>
  <?php }?>
  <div class="fdlist_1_detail">
    <div class="fdlist_1_title">
      <div class="fdlist_1_name exfd-list-name"><?php the_title(); ?></div>
      <div class="fdlist_1_price">
        <span>
        <?php if ($saleprice > 0) {?>
          <del><?php echo wp_kses_post($price); ?></del> <ins><?php echo wp_kses_post($saleprice); ?></ins>
        <?php }else{
          echo wp_kses_post($price);
        } ?>
        </span>
        
      </div>
    </div>
  </div>
  <div class="fdlist_1_des">
    <?php 
    if(has_excerpt(get_the_ID())){?>
      <p><?php echo wp_trim_words(get_the_excerpt(),$number_excerpt,'...'); ?></p>
    <?php }
    echo '<div class="ex-hidden">'; exfood_booking_button_html(1); echo '</div>';
    ?>
    <button class="exfd_modal_click exfd-choice"><div class="exfd-icon-plus"></div></button>
  </div>
</figure>