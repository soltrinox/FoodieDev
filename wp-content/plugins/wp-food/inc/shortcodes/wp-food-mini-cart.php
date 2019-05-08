<?php
function exfood_shortcode_mincart( $atts ) {
	if(phpversion()>=7){
		$atts = (array)$atts;
	}
	ob_start();
	echo '<div class="exfd-cart-content sc-min-cart">';
	exfood_template_plugin('cart-mini',1);
	echo '</div>';
	$output_string = ob_get_contents();
	ob_end_clean();
	return $output_string;
}
add_shortcode( 'ex_food_minicart', 'exfood_shortcode_mincart' );
add_action( 'after_setup_theme', 'ex_food_reg_mincart_vc' );
function ex_food_reg_mincart_vc(){
    if(function_exists('vc_map')){
	vc_map( array(
	   "name" => esc_html__("wp-food - Mini Cart", "wp-food"),
	   "base" => "ex_food_minicart",
	   "class" => "",
	   "icon" => "",
	   "controls" => "full",
	   "category" => esc_html__('wp-food','wp-food'),
	   "params" => array(
		  
	   )
	));
	}
}
