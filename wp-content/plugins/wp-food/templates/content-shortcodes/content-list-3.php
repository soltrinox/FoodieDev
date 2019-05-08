<?php
  $customlink = EX_WPFood_customlink(get_the_ID());
  global $number_excerpt;
  $price = get_post_meta( get_the_ID(), 'exfood_price', true );
  $currency = exfood_get_option('exfood_currency');
  if ($currency=='') {
    $currency ='$';
  }
  $customlink = EX_WPFood_customlink(get_the_ID());
?>
<figure class="fdstyle-list-3">
  <?php if(has_post_thumbnail(get_the_ID())){ ?>
    <a class="exfd_modal_click" href="<?php echo esc_url($customlink); ?>">
      <div class="exf-img"><?php the_post_thumbnail('exfood_80x80'); ?></div>
    </a>
  <?php }?>
    <div class="fdlist_3_title">
      <div class="fdlist_3_name exfd-list-name"><h3><?php the_title(); ?></h3></div>
    </div>
    <div class="fdlist_3_des">
      <?php 
          if(has_excerpt(get_the_ID())){?>
          <p><?php echo wp_trim_words(get_the_excerpt(),$number_excerpt,'...'); ?></p>
          <?php }?>
    </div>
    <div class="fdlist_3_order">
      <?php echo '<div class="ex-hidden">'; exfood_booking_button_html(1); echo '</div>';?>
      <button class="exfd_modal_click exfd-choice" data="food_id=<?php echo get_the_ID(); ?>&food_qty=1"><div class="exfd-icon-plus"></div></button>
    </div>
</figure>