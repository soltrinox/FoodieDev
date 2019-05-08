<?php
global $atts,$id_food;  
$customlink = EX_WPFood_customlink($id_food);
global $number_excerpt;
$price = get_post_meta( $id_food, 'exfood_price', true );
$saleprice = get_post_meta( $id_food, 'exfood_sale_price', true );
$currency = exfood_get_option('exfood_currency');
$num_decimal = exfood_get_option('exfood_num_decimal');
$decimal_sep = exfood_get_option('exfood_decimal_sep');
if ($currency=='') {
	$currency ='$';
}
$position = exfood_get_option('exfood_position');
$price = exfood_price_with_currency($price);
if ($saleprice > 0){
  $saleprice = exfood_price_with_currency($saleprice);
}
$protein = get_post_meta( $id_food, 'exfood_protein', true );
$calo = get_post_meta( $id_food, 'exfood_calo', true );
$choles = get_post_meta( $id_food, 'exfood_choles', true );
$fibel = get_post_meta( $id_food, 'exfood_fibel', true );
$sodium = get_post_meta( $id_food, 'exfood_sodium', true );
$carbo = get_post_meta( $id_food, 'exfood_carbo', true );
$fat = get_post_meta( $id_food, 'exfood_fat', true );
$gallery = get_post_meta( $id_food, 'exfood_gallery', true );

$custom_data = get_post_meta( $id_food, 'exfood_custom_data_gr', true );
$exfood_enable_rtl = exfood_get_option('exfood_enable_rtl');
$rtl_modal_mode = ($exfood_enable_rtl == 'yes') ? 'yes' : 'no';
$content = apply_filters('the_content', get_post_field('post_content', $id_food));
?>
<!-- The Modal -->
<div class="modal-content">
	<div class="ex-modal-big">
	    <span class="ex_close">&times;</span>
	    <div class="fd_modal_img">
	    	<div class="exfd-modal-carousel" rtl_mode="<?php echo esc_attr($rtl_modal_mode); ?>">
				<div><?php echo get_the_post_thumbnail($id_food,'full'); ?></div>
				<?php 
				if ($gallery != '') {
					foreach ($gallery as $item ) {
					echo '<div><img src="'.$item.'" alt="'.esc_attr(get_the_title( $id_food )).'"/></div>';
					}
				}
				?>
			</div>
	    </div>
	    <div class="fd_modal_des">
			<h3><?php echo get_the_title( $id_food ); ?></h3>
		    <div class="exfd_nutrition">
		    	<ul>
	    			<?php if($protein!=''){ ?>
		    			<li>
		    				<span><?php esc_html_e('Protein','wp-food'); ?></span><?php echo wp_kses_post($protein);?>
		    			</li>
		    		<?php }if($calo!=''){ ?>
	    				<li><span><?php esc_html_e('Calories','wp-food'); ?></span><?php echo wp_kses_post($calo);?></li>
	    			<?php }if($choles!=''){ ?>
	    				<li><span><?php esc_html_e('Cholesterol','wp-food'); ?></span><?php echo wp_kses_post($choles);?></li>
	    			<?php }if($fibel!=''){ ?>
	    				<li><span><?php esc_html_e('Dietary fibre','wp-food'); ?></span><?php echo wp_kses_post($fibel);?></li>
	    			<?php }if($sodium!=''){ ?>
	    				<li><span><?php esc_html_e('Sodium','wp-food'); ?></span><?php echo wp_kses_post($sodium);?></li>
	    			<?php }if($carbo!=''){ ?>
	    				<li><span><?php esc_html_e('Carbohydrates','wp-food'); ?></span><?php echo wp_kses_post($carbo);?></li>
	    			<?php }if($fat!=''){ ?>
	    				<li><span><?php esc_html_e('Fat total','wp-food'); ?></span><?php echo wp_kses_post($fat);?></li>
	    			<?php }
	    			if ($custom_data != '') {
	    				foreach ($custom_data as $data_it) {?>
			    			<li><span><?php echo wp_kses_post($data_it['_name']); ?></span><?php echo wp_kses_post($data_it['_value']);?></li>
			    			<?php
	    				}
	    			}
	    			?>
	    			<div class="exfd_clearfix"></div>
			    </ul>
		    </div>
		    <h5>
				<?php if ($saleprice > 0) {?>
					<del><?php echo wp_kses_post($price); ?></del> <ins><?php echo wp_kses_post($saleprice); ?></ins>
				<?php }else{
					echo wp_kses_post($price);
				} ?>
		    </h5>
		    <?php if($content!=''){?>
			    <div class="exfood-ct"><?php echo wp_kses_post($content);?></div>
			<?php }?>
		    <?php echo exfood_add_to_cart_form_shortcode( $atts );?>
	    </div>
	</div>
</div>