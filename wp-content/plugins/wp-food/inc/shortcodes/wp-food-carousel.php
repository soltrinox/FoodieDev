<?php
function exfood_shortcode_carousel( $atts ) {
	if(phpversion()>=7){
		$atts = (array)$atts;
	}
	global $ID,$number_excerpt;
	$ID = isset($atts['ID']) && $atts['ID'] !=''? $atts['ID'] : 'ex-'.rand(10,9999);
	if(!isset($atts['ID'])){
		$atts['ID']= $ID;
	}
	$style = isset($atts['style']) && $atts['style'] !=''? $atts['style'] : '1';
	$column =  '2';
	$posttype   = 'ex_food';
	$ids   = isset($atts['ids']) ? $atts['ids'] : '';
	$taxonomy  = isset($atts['taxonomy']) ? $atts['taxonomy'] : '';
	$cat   = isset($atts['cat']) ? $atts['cat'] : '';
	$tag  = isset($atts['tag']) ? $atts['tag'] : '';
	$count   = isset($atts['count']) &&  $atts['count'] !=''? $atts['count'] : '9';
	$order  = isset($atts['order']) ? $atts['order'] : '';
	$orderby  = isset($atts['orderby']) ? $atts['orderby'] : '';
	$meta_key  = isset($atts['meta_key']) ? $atts['meta_key'] : '';
	$meta_value  = isset($atts['meta_value']) ? $atts['meta_value'] : '';
	$class  = isset($atts['class']) ? $atts['class'] : '';
	$number_excerpt =  isset($atts['number_excerpt'])&& $atts['number_excerpt']!='' ? $atts['number_excerpt'] : '10';
	$slidesshow = isset($atts['slidesshow'])&& $atts['slidesshow']!='' ? $atts['slidesshow'] : '3';
	$slidesscroll =  isset($atts['slidesscroll'])&& $atts['slidesscroll']!='' ? $atts['slidesscroll'] : '';
	$autoplay 		= isset($atts['autoplay']) && $atts['autoplay'] == 1 ? 1 : 0;
	$autoplayspeed 		= isset($atts['autoplayspeed']) && is_numeric($atts['autoplayspeed']) ? $atts['autoplayspeed'] : '';
	$start_on 		= isset($atts['start_on']) ? $atts['start_on'] : '';
	$loading_effect 		= isset($atts['loading_effect']) ? $atts['loading_effect'] : '';
	$infinite 		= isset($atts['infinite']) ? $atts['infinite'] : '';
	$cart_enable  = isset($atts['cart_enable']) ? $atts['cart_enable'] : '';
	$args = EX_WPFood_query($posttype, $count, $order, $orderby, $cat, $tag, $taxonomy, $meta_key, $ids, $meta_value);
	$the_query = new WP_Query( $args );
	ob_start();
	$class = $class." style-".$style;
	if($style == 1 || $style == 3 || $style == 13 || $style == 14 || $style == 15 || $style == 16){
		$class = $class." style-classic";
	}
	$class = $class." ex-food-plug ";
	if($loading_effect == 1){
		$class = $class.' ld-screen';
	}
	if ($slidesscroll == '') {
		$slidesscroll = $slidesshow;
	}
	$html_modal ='';
	wp_enqueue_script( 'wc-add-to-cart-variation' );
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if (is_plugin_active( 'woocommerce-product-addons/woocommerce-product-addons.php' ) ) {
		$GLOBALS['Product_Addon_Display']->addon_scripts();
	}
	wp_enqueue_style( 'wpex-ex_s_lick', EXFOOD_PATH .'js/ex_s_lick/ex_s_lick.css');
	wp_enqueue_style( 'wpex-ex_s_lick-theme', EXFOOD_PATH .'js/ex_s_lick/ex_s_lick-theme.css');
	wp_enqueue_script( 'wpex-ex_s_lick', EXFOOD_PATH.'js/ex_s_lick/ex_s_lick.js', array( 'jquery' ) );
	$exfood_enable_rtl = exfood_get_option('exfood_enable_rtl');
	$locations ='';
	?>
	<div <?php if($exfood_enable_rtl=='yes'){ echo 'dir="rtl"';} ?> class="ex-fdlist ex-fdcarousel <?php echo esc_attr($class);?>" id="<?php echo esc_attr($ID);?>" data-autoplay="<?php echo esc_attr($autoplay)?>" data-speed="<?php echo esc_attr($autoplayspeed)?>" data-rtl="<?php echo esc_attr($exfood_enable_rtl)?>" data-slidesshow="<?php echo esc_attr($slidesshow)?>" data-slidesscroll="<?php echo esc_attr($slidesscroll)?>"  data-start_on="<?php echo esc_attr($start_on)?>" data-infinite="<?php echo esc_attr($infinite);?>">
		<?php exfood_select_location_html($locations);?>
		<?php 
	        if($cart_enable !='no') {
	        exfood_woo_cart_icon_html();
		    }
        ?>
    	<?php 
    	echo '<input type="hidden"  name="ajax_url" value="'.esc_url(admin_url( 'admin-ajax.php' )).'">';
    	if($loading_effect==1){?>
            <div class="exfd-loadcont"><div class="exfd-loadicon"></div></div>
        <?php } ?>
		<div class="parent_grid">
        <div class="ctgrid">
		<?php
		if ($the_query->have_posts()){
			while ($the_query->have_posts()) { $the_query->the_post();
				echo '<div  id="SearchItemNumber'. get_the_ID() .'" class="item-grid" data-id="ex_id-'.esc_attr($ID).'-'.get_the_ID().'" data-id_food="'.get_the_ID().'"  name="SearchItemElements" data-name="SearchResultsItem"> ';
					?>
					<div class="exp-arrow">
						<?php 
						exfood_template_plugin('grid-'.$style,1);
						?>
					<div class="exfd_clearfix"></div>
					</div>
					<?php

                echo ' <h4 class="ml-4" style="height: 2px">&nbsp;<span class="badge bg-black fg-white" style="margin-top:20px; margin-right:20px; " id="productIDLoopBadge';
                echo get_the_ID() ; #the_ID();
                echo '" name="productBadge" >0</span></h4>';

				echo '</div>';
			}
		} ?>
		</div>
		</div>
		<!-- Modal ajax -->
		<?php global $modal_html;
		if(!isset($modal_html) || $modal_html!='on'){
			$modal_html = 'on';
			echo "<div id='food_modal' class='ex_modal'></div>";
		}?>
	</div>
	<?php
	wp_reset_postdata();
	$output_string = ob_get_contents();
	ob_end_clean();
	return $output_string;
}
add_shortcode( 'ex_food_carousel', 'exfood_shortcode_carousel' );
add_action( 'after_setup_theme', 'exfood_reg_carousel_vc' );
function exfood_reg_carousel_vc(){
    if(function_exists('vc_map')){
	vc_map( array(
	   "name" => esc_html__("wp-food - Carousel", "wp-food"),
	   "base" => "ex_food_carousel",
	   "class" => "",
	   "icon" => "icon-grid",
	   "controls" => "full",
	   "category" => esc_html__('wp-food','wp-food'),
	   "params" => array(
		   array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Style", 'wp-food'),
			 "param_name" => "style",
			 "value" => array(
				esc_html__('1', 'wp-food') => '1',
				esc_html__('2', 'wp-food') => '2',
				esc_html__('3', 'wp-food') => '3',
				esc_html__('4', 'wp-food') => '4',
			 ),
			 "description" => esc_html__('Select style of carousel', 'wp-food')
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Count", "wp-food"),
			"param_name" => "count",
			"value" => "",
			"description" => esc_html__("Enter number of foods to show", 'wp-food'),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Number item visible", "wp-food"),
			"param_name" => "slidesshow",
			"value" => "",
			"description" => esc_html__("Number of slides to show at a time", 'wp-food'),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Number slides to scroll", "wp-food"),
			"param_name" => "slidesscroll",
			"value" => "",
			"description" => esc_html__("Number of slides to scroll at a time", 'wp-food'),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("IDs", "wp-food"),
			"param_name" => "ids",
			"value" => "",
			"description" => esc_html__("Specify food IDs to retrieve", "wp-food"),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Menu", "wp-food"),
			"param_name" => "cat",
			"value" => "",
			"description" => esc_html__("List of cat ID (or slug), separated by a comma", "wp-food"),
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Order", 'wp-food'),
			 "param_name" => "order",
			 "value" => array(
			 	esc_html__('DESC', 'wp-food') => 'DESC',
				esc_html__('ASC', 'wp-food') => 'ASC',
			 ),
			 "description" => ''
		  ),
		  array(
		  	 "admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Order by", 'wp-food'),
			 "param_name" => "orderby",
			 "value" => array(
			 	esc_html__('Date', 'wp-food') => 'date',
				esc_html__('ID', 'wp-food') => 'ID',
				esc_html__('Author', 'wp-food') => 'author',
			 	esc_html__('Title', 'wp-food') => 'title',
				esc_html__('Name', 'wp-food') => 'name',
				esc_html__('Modified', 'wp-food') => 'modified',
			 	esc_html__('Parent', 'wp-food') => 'parent',
				esc_html__('Random', 'wp-food') => 'rand',
				esc_html__('Menu order', 'wp-food') => 'menu_order',
				esc_html__('Meta value', 'wp-food') => 'meta_value',
				esc_html__('Meta value num', 'wp-food') => 'meta_value_num',
				esc_html__('Post__in', 'wp-food') => 'post__in',
				esc_html__('None', 'wp-food') => 'none',
			 ),
			 "description" => ''
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Meta key", "wp-food"),
			"param_name" => "meta_key",
			"value" => "",
			"description" => esc_html__("Enter meta key to query", "wp-food"),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Meta value", "wp-food"),
			"param_name" => "meta_value",
			"value" => "",
			"description" => esc_html__("Enter meta value to query", "wp-food"),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Number of Excerpt ( short description)", "wp-food"),
			"param_name" => "number_excerpt",
			"value" => "",
			"description" => esc_html__("Enter number of Excerpt, enter:0 to disable excerpt", "wp-food"),
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Autoplay", 'wp-food'),
			 "param_name" => "autoplay",
			 "value" => array(
			 	esc_html__('No', 'wp-food') => '',
				esc_html__('Yes', 'wp-food') => '1',
			 ),
			 "description" => ''
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "textfield",
			 "class" => "",
			 "heading" => esc_html__("Autoplay Speed", "wp-food"),
			 "param_name" => "autoplayspeed",
			 "value" => "",
			 "dependency" 	=> array(
				'element' => 'autoplay',
				'value'   => array('1'),
			 ),
			 "description" => esc_html__("Autoplay Speed in milliseconds. Default:3000", "wp-food"),
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Enable Loading effect", "wp-food"),
			 "param_name" => "loading_effect",
			 "value" => array(
			 	esc_html__('No', 'wp-food') => '',
			 	esc_html__('Yes', 'wp-food') => '1',
			 ),
			 "description" => ""
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Infinite", "wp-food"),
			 "param_name" => "infinite",
			 "value" => array(
			 	esc_html__('No', 'wp-food') => '',
			 	esc_html__('Yes', 'wp-food') => 'yes',
			 ),
			 "description" => esc_html__("Infinite loop sliding ( go to first item when end loop)", "wp-food"),
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Enable cart", 'wp-food'),
			 "param_name" => "cart_enable",
			 "value" => array(
			 	esc_html__('Yes', 'wp-food') => '',
			 	esc_html__('No', 'wp-food') => 'no',
			 ),
			 "description" => esc_html__("Enable side cart icon", "wp-food"),
		  ),
	   )
	));
	}
}