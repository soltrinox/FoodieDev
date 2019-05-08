<?php
function exfood_shortcode_grid( $atts ) {
	if(phpversion()>=7){
		$atts = (array)$atts;
	}
	global $ID,$number_excerpt,$img_size;
	$ID = isset($atts['ID']) && $atts['ID'] !=''? $atts['ID'] : 'ex-'.rand(10,9999);
	if(!isset($atts['ID'])){
		$atts['ID']= $ID;
	}
	$style = isset($atts['style']) && $atts['style'] !=''? $atts['style'] : '1';
	$column = isset($atts['column']) && $atts['column'] !=''? $atts['column'] : '2';
	$posttype = 'ex_food';
	$ids   = isset($atts['ids']) ? $atts['ids'] : '';
	$taxonomy  = isset($atts['taxonomy']) ? $atts['taxonomy'] : '';
	$cat   = isset($atts['cat']) ? $atts['cat'] : '';
	$tag  = isset($atts['tag']) ? $atts['tag'] : '';
	$count   = isset($atts['count']) &&  $atts['count'] !=''? $atts['count'] : '9';
	$menu_filter   = isset($atts['menu_filter']) ? $atts['menu_filter'] : 'hide';
	$posts_per_page   = isset($atts['posts_per_page']) && $atts['posts_per_page'] !=''? $atts['posts_per_page'] : '3';
	$order  = isset($atts['order']) ? $atts['order'] : '';
	$orderby  = isset($atts['orderby']) ? $atts['orderby'] : '';
	$meta_key  = isset($atts['meta_key']) ? $atts['meta_key'] : '';
	$meta_value  = isset($atts['meta_value']) ? $atts['meta_value'] : '';
	$class  = isset($atts['class']) ? $atts['class'] : '';
	$page_navi  = isset($atts['page_navi']) ? $atts['page_navi'] : '';
	$number_excerpt =  isset($atts['number_excerpt'])&& $atts['number_excerpt']!='' ? $atts['number_excerpt'] : '10';
	$cart_enable  = isset($atts['cart_enable']) ? $atts['cart_enable'] : '';
	$img_size =  isset($atts['img_size']) ? $atts['img_size'] :'';
	$paged = get_query_var('paged')?get_query_var('paged'):(get_query_var('page')?get_query_var('page'):1);
	$args = EX_WPFood_query($posttype, $posts_per_page, $order, $orderby, $cat, $tag, 'exfood_cat', $meta_key, $ids, $meta_value);
	
	$the_query = new WP_Query( $args );
	ob_start();
	$class = $class." ex-food-plug ";
	$class = $class." column-".$column;
	$class = $class." style-".$style;
	if($style == 1 || $style == 3 || $style == 13 || $style == 14 || $style == 15 || $style == 16){
		$class = $class." style-classic";
	}
	$html_modal ='';
	wp_enqueue_script( 'wc-add-to-cart-variation' );
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if (is_plugin_active( 'woocommerce-product-addons/woocommerce-product-addons.php' ) ) {
		$GLOBALS['Product_Addon_Display']->addon_scripts();
	}
	$locations ='';
	?>
	<div class="ex-fdlist <?php echo esc_attr($class);?>" id ="<?php echo esc_attr($ID);?>">
		<?php exfood_select_location_html($locations);?>
        <?php if($menu_filter=="show") {exfood_search_form_html($cat);}
	        if($cart_enable !='no') {
	        exfood_woo_cart_icon_html();
		    }
        ?>
        <div class="parent_grid">
        <div class="ctgrid">
		<?php
		$num_pg = '';
		$arr_ids = array();
		if ($the_query->have_posts()){ 
			$i=0;
			$it = $the_query->found_posts;
			if($it < $count || $count=='-1'){ $count = $it;}
			if($count  > $posts_per_page){
				$num_pg = ceil($count/$posts_per_page);
				$it_ep  = $count%$posts_per_page;
			}else{
				$num_pg = 1;
			}

            $idct = 0;

			while ($the_query->have_posts()) { $the_query->the_post();
				$arr_ids[] = get_the_ID();
				$i++;
				if(($num_pg == $paged) && $num_pg!='1'){
					if($i > $it_ep){ break;}
				}
				echo '<div class="item-grid" data-id="ex_id-'.esc_attr($ID).'-'.get_the_ID().'" data-id_food="'.get_the_ID().'"   name="SearchItemElements" data-name="SearchResultsItem" > ';
					?>
					<div class="exp-arrow">
						<?php 
						exfood_template_plugin('grid-'.$style,1);
						?>
					<div class="exfd_clearfix"></div>
					</div>
					<?php
                $idct++;
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
		<?php
		if($page_navi=='loadmore'){
			exfood_ajax_navigate_html($ID,$atts,$num_pg,$args,$arr_ids); 
		}else{ ?>
			<div class="exfd-pagination-parent">
			<?php exfood_page_number_html($the_query,$ID,$atts,$num_pg,$args,$arr_ids);?>
			</div>
		<?php }
		?>

	</div>
	<?php
	wp_reset_postdata();
	$output_string = ob_get_contents();
	ob_end_clean();
	return $output_string;
}
add_shortcode( 'ex_food_grid', 'exfood_shortcode_grid' );
add_action( 'after_setup_theme', 'ex_food_reg_food_grid_vc' );
function ex_food_reg_food_grid_vc(){
    if(function_exists('vc_map')){
	vc_map( array(
	   "name" => esc_html__("wp-food - Grid", "wp-food"),
	   "base" => "ex_food_grid",
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
			 "description" => esc_html__('Select style of grid', 'wp-food')
		  ),
		   array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Columns", 'wp-food'),
			 "param_name" => "column",
			 "value" => array(
				esc_html__('2 columns', 'wp-food') => '2',
				esc_html__('3 columns', 'wp-food') => '3',
				esc_html__('4 columns', 'wp-food') => '4',
				esc_html__('5 columns', 'wp-food') => '5',
			 ),
			 "description" => esc_html__('Select number column of grid', 'wp-food')
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
			"heading" => esc_html__("Food per page", "wp-food"),
			"param_name" => "posts_per_page",
			"value" => "",
			"description" => esc_html__("Number food per page", 'wp-food'),
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
			 "heading" => esc_html__("Page navi", 'wp-food'),
			 "param_name" => "page_navi",
			 "value" => array(
			 	esc_html__('Number', 'wp-food') => '',
				esc_html__('Load more', 'wp-food') => 'loadmore',
			 ),
			 "description" => esc_html__("Select type of page navigation", "wp-food"),
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Menu filter", 'wp-food'),
			 "param_name" => "menu_filter",
			 "value" => array(
			 	esc_html__('Hide', 'wp-food') => 'hide',
			 	esc_html__('Show', 'wp-food') => 'show',
			 ),
			 "description" => esc_html__("Select show or hide Menu filter", "wp-food"),
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