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

  $theItemID = get_the_ID();
?>
<figure class="exstyle-1 tppost-<?php the_ID();?>" id="itemNode<?php the_ID() ; ?>"  name="itemNodes" >

  <div class="exstyle-1-image">
    <a class="exfd_modal_click" href="<?php echo esc_url($customlink); ?>">
      <?php the_post_thumbnail($img_size); ?>

    </a>
    <?php exfood_sale_badge($saleprice); ?>
  </div><figcaption>
        <h3><a class="exfd_modal_click" href="<?php echo esc_url($customlink); ?>"  id="itemNodeLink<?php the_ID() ; ?>"  name="itemNodeLink"  >
                <span id="itemNodeName<?php the_ID() ; ?>"  name="itemNodeName"><?php the_title(); ?></span></a>
        </h3>
    <h5>
      <p>
        <span  id="itemNodePrice<?php the_ID() ; ?>"  name="itemNodePrice"  >
          <?php if ($saleprice > 0) {?>
            <del><?php echo wp_kses_post($price); ?></del> <ins><?php echo wp_kses_post($saleprice); ?></ins>
          <?php }else{
            echo wp_kses_post($price);
          } ?>
        </span>
      </p>
    </h5>
    <?php 
      if(has_excerpt(get_the_ID())){?>
          <p  id="itemNodeDesc<?php the_ID() ; ?>"  name="itemNodeDesc"  ><?php echo wp_trim_words(get_the_excerpt(),$number_excerpt,'...'); ?></p>
      <?php }?>
      <?php
      exfood_booking_button_html(1);
    ?>
  </figcaption>
</figure>