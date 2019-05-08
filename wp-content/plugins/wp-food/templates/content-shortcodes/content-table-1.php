<?php
$customlink = EX_WPFood_customlink(get_the_ID());
  global $number_excerpt,$attr,$id_food;
  $id_food =get_the_ID();
  $order_price=0;
  $price = get_post_meta( get_the_ID(), 'exfood_price', true );
  $saleprice = get_post_meta( get_the_ID(), 'exfood_sale_price', true );
  $num_decimal = exfood_get_option('exfood_num_decimal');
  $decimal_sep = exfood_get_option('exfood_decimal_sep');
  if ($saleprice > 0) {
    $order_price = $saleprice;
  }else{
    $order_price = $price;
  }
  $currency = exfood_get_option('exfood_currency');
  $category = get_the_terms(get_the_ID(),'exfood_cat');
  $menu ='';
  if(!empty($category)){
    foreach($category as $cd){
      $cat = get_category( $cd );
      $menu .= '<p>'.$cat->name.'</p>';
    }
  }
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
<tr data-id_food="<?php echo get_the_ID()?>"   id="SearchItemNumber<?php get_the_ID(); ?>" >
  <td><a href="<?php echo esc_url($customlink); ?>"><?php the_post_thumbnail('exfood_80x80'); ?></a></td>
  <td id="extd-<?php echo get_the_ID()?>" class="ex-fd-name" data-sort="<?php echo esc_attr(get_the_title());?>">
    <?php echo '<div class="item-grid tppost-'.get_the_ID().'" ';?>
      <div class="exp-arrow">
        <h3><a href="<?php echo esc_url($customlink); ?>"><?php the_title(); ?></a></h3>
        <span class="exfd-show-tablet">
          <?php echo esc_html_e( 'Category:', 'wp-food' ).wp_kses_post($menu); ?>
        </span>
        <div class="exfd-hide-mb">
          <div class="exfd-price-detail">
            <?php if ($saleprice > 0) {?>
              <del><?php echo wp_kses_post($price); ?></del> <ins><?php echo wp_kses_post($saleprice); ?></ins>
            <?php }else{
              echo wp_kses_post($price);
            } ?>
          </div>
          <?php if($number_excerpt != '0'){?>
            <?php if(has_excerpt(get_the_ID())){?>
              <p><?php echo wp_trim_words(get_the_excerpt(),$number_excerpt,'...'); ?></p>
            <?php } ?>  
          <?php }?>
        </div>
      </div>
    </div>
  </td>
  <?php if($number_excerpt != '0'){?>
  <td class="exfd-hide-screen ex-fd-table-des">
      <?php if(has_excerpt(get_the_ID())){?>
            <p><?php echo wp_trim_words(get_the_excerpt(),$number_excerpt,'...'); ?></p>
      <?php } ?>
  </td>
  <?php }?>
  
  <td class="exfd-hide-screen exfd-hide-tablet ex-fd-category" data-sort="<?php echo esc_attr($menu);?>">
    <?php echo wp_kses_post($menu); ?>
  </td>

  <td class="exfd-hide-screen exfd-price" data-sort="<?php echo esc_attr($order_price);?>">
    <div class="exfd-price-detail">
    <?php if ($saleprice > 0) {?>
      <del><?php echo wp_kses_post($price); ?></del> <ins><?php echo wp_kses_post($saleprice); ?></ins>
    <?php }else{
      echo wp_kses_post($price);
    } ?>
    </div>
  </td>
  <td class="ex-fd-table-order">
    <?php echo '<div class="ex-hidden">'; exfood_booking_button_html(1); echo '</div>'; ?>
    <button class="exfd_modal_click exfd-choice" data="food_id=<?php echo esc_attr(get_the_ID()); ?>&food_qty=1"><div class="exfd-icon-plus"></div></button>
  </td>
</tr>
