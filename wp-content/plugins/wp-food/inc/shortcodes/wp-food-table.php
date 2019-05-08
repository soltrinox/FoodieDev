<?php
function exfood_shortcode_table( $atts ) {
	if(phpversion()>=7){
		$atts = (array)$atts;
	}
	global $ID, $count, $posts_per_page,$number_excerpt;
	$ID = isset($atts['ID']) ? $atts['ID'] : rand(10,9999);
	if(!isset($atts['ID'])){
		$atts['ID']= $ID;
	}
	$style = isset($atts['style']) ? $atts['style'] : '1';
	$posttype   =  'ex_food';
	$ids   = isset($atts['ids']) ? $atts['ids'] : '';
	$taxonomy  = isset($atts['taxonomy']) ? $atts['taxonomy'] : '';
	$cat   = isset($atts['cat']) ? $atts['cat'] : '';
	$tag  = isset($atts['tag']) ? $atts['tag'] : '';
	$count   = isset($atts['count']) &&  $atts['count'] !=''? $atts['count'] : '9';
	$posts_per_page   = isset($atts['posts_per_page']) && $atts['posts_per_page'] !=''? $atts['posts_per_page'] : '3';
	$order  = isset($atts['order']) ? $atts['order'] : '';
	$orderby  = isset($atts['orderby']) ? $atts['orderby'] : '';
	$meta_key  = isset($atts['meta_key']) ? $atts['meta_key'] : '';
	$meta_value  = isset($atts['meta_value']) ? $atts['meta_value'] : '';
	$class  = isset($atts['class']) ? $atts['class'] : '';
	$page_navi  = isset($atts['page_navi']) ? $atts['page_navi'] : '';
	$live_sort =  isset($atts['live_sort']) ? $atts['live_sort'] :'';
	$menu_filter   = isset($atts['menu_filter']) ? $atts['menu_filter'] : 'hide';
	$cart_enable  = isset($atts['cart_enable']) ? $atts['cart_enable'] : '';
	$paged = get_query_var('paged')?get_query_var('paged'):(get_query_var('page')?get_query_var('page'):1);
	$number_excerpt =  isset($atts['number_excerpt'])&& $atts['number_excerpt']!='' ? $atts['number_excerpt'] : '10';
	$args = EX_WPFood_query($posttype, $posts_per_page, $order, $orderby, $cat, $tag, $taxonomy, $meta_key, $ids, $meta_value);
	global $the_query;
	$the_query = new WP_Query( $args );
	ob_start();
	global $html_modal;
	$html_modal ='';
	$ID = 'table-'.$ID;
	$class = $class." ex-food-plug ";
	$locations ='';
	wp_enqueue_script( 'wc-add-to-cart-variation' );
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if (is_plugin_active( 'woocommerce-product-addons/woocommerce-product-addons.php' ) ) {
		$GLOBALS['Product_Addon_Display']->addon_scripts();
	}
	?>
	<div class="ex-fdlist table-layout <?php echo esc_attr($class); if($live_sort=='1'){ echo ' table-lv-sort';}?>" id ="<?php echo esc_attr($ID);?>">
        <?php exfood_select_location_html($locations);?>
        <?php if($menu_filter=="show") {exfood_search_form_html($cat);}?>
        <?php if($cart_enable !='no') {
	        exfood_woo_cart_icon_html();
		    };?>
		<div class="ctlist">
		<?php if($live_sort=='1'){
			?>
			<script type="text/javascript">
				jQuery(document).ready(function ($) {
					if(!jQuery.fn.sortElements){
						jQuery.fn.sortElements = (function(){
							var sort = [].sort;
							return function(comparator, getSortable) {
								getSortable = getSortable || function(){return this;};
								var placements = this.map(function(){
									var sortElement = getSortable.call(this),
										parentNode = sortElement.parentNode,
										nextSibling = parentNode.insertBefore(
											document.createTextNode(''),
											sortElement.nextSibling
										);
									return function() {
										if (parentNode === this) {
											throw new Error(
												"You can't sort elements if any one is a descendant of another."
											);
										}
										parentNode.insertBefore(this, nextSibling);
										parentNode.removeChild(nextSibling);
									};
								});
								return sort.call(this, comparator).each(function(i){
									placements[i].call(getSortable.call(this));
								});
								
							};
						})();							
					}
					var table = $('#<?php echo $ID;?>');
					$('#<?php echo $ID;?> .exp-sort')
						.each(function(){
							var th = $(this),
								thIndex = th.index(),
								inverse = false;
							th.on('click', function(){
								$('#<?php echo $ID;?> th').removeClass('s-descending');
								$('#<?php echo $ID;?> th').removeClass('s-ascending');
								if(inverse == true){
									$(this).addClass('s-descending');
									$(this).removeClass('s-ascending');
								}else{
									$(this).removeClass('s-descending');
									$(this).addClass('s-ascending');
								}
								table.find('td').filter(function(){
									return $(this).index() === thIndex;
								}).sortElements(function(a, b){
									return $(a).data('sort') > $(b).data('sort') ?
										inverse ? -1 : 1
										: inverse ? 1 : -1;
								}, function(){
									// parentNode is the element we want to move
									return this.parentNode; 
								});
								inverse = !inverse;
							});
					});
				});
			</script>
		<?php }?>
		
        <table class="exfd-table-<?php echo esc_attr($style); ?> <?php if($number_excerpt != '0') echo "exfd-non-showdes"?>">
            <?php if($style==1){?>
            <thead>
                <tr>
                    <th><?php echo esc_html__('Image','wp-food');?></th>
                    <th class="exp-sort ex-fd-name"><span class ="exfd-hide-screen"><?php echo esc_html__('Name','wp-food');?></span><span class="exfd-hide-mb  ex-detail"><?php echo esc_html__('Detail','wp-food');?></th>
                    <th class="exfd-hide-screen ex-fd-table-des"><?php echo esc_html__('Description','wp-food');?></th>
                    <th class="exp-sort exfd-hide-screen exfd-hide-tablet ex-fd-category"><?php echo esc_html__('Menu','wp-food');?></th>
                    
                    <th class="exp-sort exfd-hide-screen exfd-price"><?php echo esc_html__('Price','wp-food');?></th>
                    <th class="ex-fd-table-order"></th>
                </tr>
            </thead>
            <?php }?>
            <tbody>
                <?php 
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
				$arr_ids = array();
                while ($the_query->have_posts()) { $the_query->the_post();
					$arr_ids[] = get_the_ID();
					$i++;
					if(($num_pg == $paged) && $num_pg!='1'){
						if($i > $it_ep){ break;}
					}
                    exfood_template_plugin('table-'.$style,1);?>
                    <?php
                  }
                } ?>
            </tbody>
        </table>
           
		</div>
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
add_shortcode( 'ex_food_table', 'exfood_shortcode_table' );
add_action( 'after_setup_theme', 'ex_food_reg_table_vc' );
function ex_food_reg_table_vc(){
    if(function_exists('vc_map')){
	vc_map( array(
	   "name" => esc_html__("wp-food - Table", "wp-food"),
	   "base" => "ex_food_table",
	   "class" => "",
	   "icon" => "icon-grid",
	   "controls" => "full",
	   "category" => esc_html__('wp-food','wp-food'),
	   "params" => array(
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
			 "heading" => esc_html__("Live Sort", 'wp-food'),
			 "param_name" => "live_sort",
			 "value" => array(
			 	esc_html__('No', 'wp-food') => '',
				esc_html__('Yes', 'wp-food') => '1',
			 ),
			 "description" => esc_html__("Enable Live Sort", "wp-food"),
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
