<?php
//shortcode
include plugin_dir_path(__FILE__).'shortcodes/wp-food-list.php';
include plugin_dir_path(__FILE__).'shortcodes/wp-food-grid.php';
include plugin_dir_path(__FILE__).'shortcodes/wp-food-table.php';
include plugin_dir_path(__FILE__).'shortcodes/wp-food-carousel.php';
include plugin_dir_path(__FILE__).'shortcodes/wp-food-mini-cart.php';
include plugin_dir_path(__FILE__).'shortcodes/wp-food-user.php';
//widget
include plugin_dir_path(__FILE__).'widgets/wp-food.php';

if(!function_exists('exfood_startsWith')){
	function exfood_startsWith($haystack, $needle)
	{
		return !strncmp($haystack, $needle, strlen($needle));
	}
} 
if(!function_exists('exfood_get_google_fonts_url')){
	function exfood_get_google_fonts_url ($font_names) {
	
		$font_url = '';
	
		$font_url = add_query_arg( 'family', urlencode(implode('|', $font_names)) , "//fonts.googleapis.com/css" );
		return $font_url;
	} 
}
if(!function_exists('exfood_get_google_font_name')){
	function exfood_get_google_font_name($family_name){
		$name = $family_name;
		if(exfood_startsWith($family_name, 'http')){
			// $family_name is a full link, so first, we need to cut off the link
			$idx = strpos($name,'=');
			if($idx > -1){
				$name = substr($name, $idx);
			}
		}
		$idx = strpos($name,':');
		if($idx > -1){
			$name = substr($name, 0, $idx);
			$name = str_replace('+',' ', $name);
		}
		return $name;
	}
}
if(!function_exists('exfood_template_plugin')){
	function exfood_template_plugin($pageName,$shortcode=false){
		if(isset($shortcode) && $shortcode== true){
			if (locate_template('wp-food/content-shortcodes/content-' . $pageName . '.php') != '') {
				get_template_part('wp-food/content-shortcodes/content', $pageName);
			} else {
				include exfood_get_plugin_url().'templates/content-shortcodes/content-' . $pageName . '.php';
			}
		}else{
			if (locate_template('wp-food/' . $pageName . '.php') != '') {
				get_template_part('wp-food/', $pageName);
			} else {
				include exfood_get_plugin_url().'templates/' . $pageName . '.php';
			}
		}
	}
}

if(!function_exists('EX_WPFood_query')){
    function EX_WPFood_query($posttype, $count, $order, $orderby, $cat, $tag, $taxonomy, $meta_key, $ids, $meta_value=false,$page=false,$mult=false){
		$posttype = 'ex_food';
		if($orderby == 'timeline_date'){
			$meta_key = 'wptl_orderdate';
			$orderby = 'meta_value_num';
		}
		if($posttype == 'ex_food' && $taxonomy == ''){
			$taxonomy = 'exfood_cat';
		}
		$posttype = explode(",", $posttype);
		if($ids!=''){ //specify IDs
			$ids = explode(",", $ids);
			$args = array(
				'post_type' => $posttype,
				'posts_per_page' => $count,
				'post_status' => array( 'publish', 'future' ),
				'post__in' =>  $ids,
				'order' => $order,
				'orderby' => $orderby,
				'ignore_sticky_posts' => 1,
			);
		}elseif($ids==''){
			$args = array(
				'post_type' => $posttype,
				'posts_per_page' => $count,
				'post_status' => array( 'publish', 'future' ),
				'tag' => $tag,
				'order' => $order,
				'orderby' => $orderby,
				'meta_key' => $meta_key,
				'ignore_sticky_posts' => 1,
			);
			if(!is_array($cat) && $taxonomy =='') {
				$cats = explode(",",$cat);
				if(is_numeric($cats[0])){
					$args['category__in'] = $cats;
				}else{			 
					$args['category_name'] = $cat;
				}
			}elseif( is_array($cat) && count($cat) > 0 && $taxonomy ==''){
				$args['category__in'] = $cat;
			}
			if($taxonomy !='' && $tag!=''){
				$tags = explode(",",$tag);
				if(is_numeric($tags[0])){$field_tag = 'term_id'; }
				else{ $field_tag = 'slug'; }
				if(count($tags)>1){
					  $texo = array(
						  'relation' => 'OR',
					  );
					  foreach($tags as $iterm) {
						  $texo[] = 
							  array(
								  'taxonomy' => $taxonomy,
								  'field' => $field_tag,
								  'terms' => $iterm,
							  );
					  }
				  }else{
					  $texo = array(
						  array(
								  'taxonomy' => $taxonomy,
								  'field' => $field_tag,
								  'terms' => $tags,
							  )
					  );
				}
			}
			//cats
			if($taxonomy !='' && $cat!=''){
				$cats = explode(",",$cat);
				if(is_numeric($cats[0])){$field = 'term_id'; }
				else{ $field = 'slug'; }
				if(count($cats)>1){
					  $texo = array(
						  'relation' => 'OR',
					  );
					  foreach($cats as $iterm) {
						  $texo[] = 
							  array(
								  'taxonomy' => $taxonomy,
								  'field' => $field,
								  'terms' => $iterm,
							  );
					  }
				  }else{
					  $texo = array(
						  array(
								  'taxonomy' => $taxonomy,
								  'field' => $field,
								  'terms' => $cats,
							  )
					  );
				}
			}
			if(isset($mult) && $mult!=''){
				$texo['relation'] = 'AND';
				$texo[] = 
					array(
						'taxonomy' => 'wpex_category',
						'field' => 'term_id',
						'terms' => $mult,
					);
			}
			// user select loc
			//check if ( exfood_get_option('exfood_enable_loc') =='yes' ) {
				$loc = isset($_SESSION['ex_userloc']) && $_SESSION['ex_userloc']!='' ? $_SESSION['ex_userloc'] :'';
				if($loc!=''){
					$loc = explode(",",$loc);
					if(is_numeric($loc[0])){$field = 'term_id'; }
					else{ $field = 'slug'; }
					if(!isset($texo) || !is_array($texo)){ $texo = array();}
					$texo['relation'] = 'AND';
					if(count($loc)>1){
						  foreach($loc as $iterm) {
							  $texo[] = 
								  array(
									  'taxonomy' => 'exfood_loc',
									  'field' => $field,
									  'terms' => $iterm,
								  );
						  }
					  }else{
						  $texo[] = 
							  array(
									  'taxonomy' => 'exfood_loc',
									  'field' => $field,
									  'terms' => $loc,
						  );
					}
				}
			//}
			if(isset($texo)){
				$args += array('tax_query' => $texo);
			}
		}
		if(isset($meta_value) && $meta_value!='' && $meta_key!=''){
			if(!empty($args['meta_query'])){
				$args['meta_query']['relation'] = 'AND';
			}
			$args['meta_query'][] = array(
				'key'  => $meta_key,
				'value' => $meta_value,
				'compare' => '='
			);
		}	
		if(isset($page) && $page!=''){
			$args['paged'] = $page;
		}
		return apply_filters( 'exfood_query', $args );
	}
}


if(!function_exists('EX_WPFood_customlink')){
	function EX_WPFood_customlink($id){
		if ( exfood_get_option('exfood_disable_single') =='yes' ) {
			return 'javascript:;';
		}
		return get_the_permalink($id);
	}
}


if(!function_exists('exfood_page_number_html')){
	if(!function_exists('exfood_page_number_html')){
		function exfood_page_number_html($the_query,$ID,$atts,$num_pg,$args,$arr_ids){
			if(function_exists('paginate_links')) {
				echo '<div class="exfd-pagination">';
				echo '
					<input type="hidden"  name="id_grid" value="'.$ID.'">
					<input type="hidden"  name="num_page" value="'.$num_pg.'">
					<input type="hidden"  name="num_page_uu" value="1">
					<input type="hidden"  name="current_page" value="1">
					<input type="hidden"  name="ajax_url" value="'.esc_url(admin_url( 'admin-ajax.php' )).'">
					<input type="hidden"  name="param_query" value="'.esc_html(str_replace('\/', '/', json_encode($args))).'">
					<input type="hidden"  name="param_ids" value="'.esc_html(str_replace('\/', '/', json_encode($arr_ids))).'">
					<input type="hidden" id="param_shortcode" name="param_shortcode" value="'.esc_html(str_replace('\/', '/', json_encode($atts))).'">
				';
				if($num_pg > 1){
					$page_link =  paginate_links( array(
						'base'         => esc_url_raw( str_replace( 999999999, '%#%', get_pagenum_link( 999999999, false ) ) ),
						'format'       => '?paged=%#%',
						'add_args'     => false,
						'show_all'     => true,
						'current' => max( 1, get_query_var('paged') ),
						'total' => $num_pg,
						'prev_next'    => false,
						'type'         => 'array',
						'end_size'     => 3,
						'mid_size'     => 3
					) );
					$class = '';
					if ( get_query_var('paged')<2) {
						$class = 'disable-click';
					}
					$prev_link = '<a class="prev-ajax '.$class.'" href="javascript:;">&larr;</a>';
					$next_link = '<a class="next-ajax" href="javascript:;">&rarr;</a>';
					array_unshift($page_link, $prev_link);
					$page_link[] = $next_link;
					echo '<div class="page-navi">'.implode($page_link).'</div>';
				}
				echo '</div>';
			}
		}
	}
}

if(!function_exists('exfood_ajax_navigate_html')){
	function exfood_ajax_navigate_html($ID,$atts,$num_pg,$args,$arr_ids){
		echo '
			<div class="ex-loadmore">
				<input type="hidden"  name="id_grid" value="'.esc_attr($ID).'">
				<input type="hidden"  name="num_page" value="'.esc_attr($num_pg).'">
				<input type="hidden"  name="num_page_uu" value="1">
				<input type="hidden"  name="current_page" value="1">
				<input type="hidden"  name="ajax_url" value="'.esc_url(admin_url( 'admin-ajax.php' )).'">
				<input type="hidden"  name="param_query" value="'.esc_html(str_replace('\/', '/', json_encode($args))).'">
				<input type="hidden"  name="param_ids" value="'.esc_html(str_replace('\/', '/', json_encode($arr_ids))).'">
				<input type="hidden" id="param_shortcode" name="param_shortcode" value="'.esc_html(str_replace('\/', '/', json_encode($atts))).'">';
				if($num_pg > 1){
					echo '
					<a  href="javascript:void(0)" class="loadmore-exfood" data-id="'.esc_attr($ID).'">
						<span class="load-text">'.esc_html__('Load more','wp-food').'</span><span></span>&nbsp;<span></span>&nbsp;<span></span>
					</a>';
				}
				echo '
		</div>';
	}
}

add_action( 'wp_ajax_exfood_loadmore', 'ajax_exfood_loadmore' );
add_action( 'wp_ajax_nopriv_exfood_loadmore', 'ajax_exfood_loadmore' );
function ajax_exfood_loadmore(){
	global $columns,$number_excerpt,$show_time,$orderby,$img_size,$ID;
	global $ID,$number_excerpt,$img_size;
	$atts = json_decode( stripslashes( $_POST['param_shortcode'] ), true );
	$ID = isset($atts['ID']) && $atts['ID'] !=''? $atts['ID'] : 'ex-'.rand(10,9999);
	$style = isset($atts['style']) && $atts['style'] !=''? $atts['style'] : '1';
	$column = isset($atts['column']) && $atts['column'] !=''? $atts['column'] : '2';
	$posttype   = isset($atts['posttype']) && $atts['posttype']!='' ? $atts['posttype'] : 'ex_food';
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
	$img_size =  isset($atts['img_size']) ? $atts['img_size'] :'';
	$number_excerpt =  isset($atts['number_excerpt'])&& $atts['number_excerpt']!='' ? $atts['number_excerpt'] : '10';
	$page = $_POST['page'];
	$layout = isset($_POST['layout']) ? $_POST['layout'] : '';
	$param_query = json_decode( stripslashes( $_POST['param_query'] ), true );
	$param_ids = '';
	if(isset($_POST['param_ids']) && $_POST['param_ids']!=''){
		$param_ids =  json_decode( stripslashes( $_POST['param_ids'] ), true )!='' ? json_decode( stripslashes( $_POST['param_ids'] ), true ) : explode(",",$_POST['param_ids']);
	}
	$end_it_nb ='';
	if($page!=''){ 
		$param_query['paged'] = $page;
		$count_check = $page*$posts_per_page;
		if(($count_check > $count) && (($count_check - $count)< $posts_per_page)){$end_it_nb = $count - (($page - 1)*$posts_per_page);}
		else if(($count_check > $count)) {die;}
	}
	if($orderby =='rand' && is_array($param_ids)){
		$param_query['post__not_in'] = $param_ids;
		$param_query['paged'] = 1;
	}
	global $wpdb;
	$first_char = isset($_POST['char']) ? $_POST['char'] : '';
	if($first_char!=''){
		$postids = $wpdb->get_col($wpdb->prepare("
			SELECT      ID
			FROM        $wpdb->posts
			WHERE       post_type = 'ex_food' AND SUBSTR($wpdb->posts.post_title,1,1) = %s
			ORDER BY    $wpdb->posts.post_title",
			$first_char)
		);
		if(!empty($postids)){
			$param_query['post__in'] = $postids;
		}else{
			echo esc_html__('No matching records found','wp-food');die;
		}
	}
	
	$the_query = new WP_Query( $param_query );
	$it = $the_query->post_count;
	ob_start();
	if($the_query->have_posts()){
		$i =0;
		$arr_ids = array();
		$html_modal = '';
		while($the_query->have_posts()){ $the_query->the_post();
			$i++;
			$arr_ids[] = get_the_ID();
			if($layout=='table'){
				exfood_template_plugin('table-'.$style,1);
			}else if($layout=='list'){
				echo '<div class="fditem-list item-grid" data-id="ex_id-'.esc_attr($ID).'-'.get_the_ID().'" data-id_food="'.get_the_ID().'"   name="SearchItemElements" data-name="SearchResultsItem" > ';
						?>
					<div class="exp-arrow" >
						<?php 
						exfood_template_plugin('list-'.$style,1);
						?>
					<div class="exfd_clearfix"></div>
					</div>
					<?php

                echo ' <h4 class="ml-4" style="height: 2px">&nbsp;<span class="badge bg-black fg-white" style="margin-top:20px; margin-right:20px; " id="productIDLoopBadge';
                echo get_the_ID() ; #the_ID();
                echo '" name="productBadge" >0</span></h4>';

				echo '</div>';
			}else{
				echo '<div class="item-grid" data-id="ex_id-'.esc_attr($ID).'-'.get_the_ID().'" data-id_food="'.get_the_ID().'"  name="SearchItemElements" data-name="SearchResultsItem" > ';
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
			if($end_it_nb!='' && $end_it_nb == $i){break;}
		}
		wp_reset_postdata();
		
		if($orderby =='rand' && is_array($param_ids)){
		
		}?>
        </div>
        <?php
	}
	$html = ob_get_clean();
	$output =  array('html_content'=>$html,'html_modal'=> $html_modal);
	echo str_replace('\/', '/', json_encode($output));
	die;
}

add_action( 'wp_ajax_exfood_category', 'ajax_exfood_category' );
add_action( 'wp_ajax_nopriv_exfood_category', 'ajax_exfood_category' );
function ajax_exfood_category(){
	global $ID,$number_excerpt,$img_size;
	$atts = json_decode( stripslashes( $_POST['param_shortcode'] ), true );
	$ID = isset($atts['ID']) && $atts['ID'] !=''? $atts['ID'] : 'ex-'.rand(10,9999);
	$ids   = isset($atts['ids']) ? $atts['ids'] : '';
	$count   = isset($atts['count']) &&  $atts['count'] !=''? $atts['count'] : '9';
	$style = isset($atts['style']) && $atts['style'] !=''? $atts['style'] : '1';
	$posts_per_page   = isset($atts['posts_per_page']) && $atts['posts_per_page'] !=''? $atts['posts_per_page'] : '3';
	$number_excerpt =  isset($atts['number_excerpt'])&& $atts['number_excerpt']!='' ? $atts['number_excerpt'] : '10';
	$cat   = isset($atts['cat']) ? $atts['cat'] : '';
	$page_navi  = isset($atts['page_navi']) ? $atts['page_navi'] : '';
	$img_size =  isset($atts['img_size']) ? $atts['img_size'] :'';
	$page = $_POST['page'];
	$layout = isset($_POST['layout']) ? $_POST['layout'] : '';
	$param_query = json_decode( stripslashes( $_POST['param_query'] ), true );
	$param_ids = '';
	if(isset($_POST['param_ids']) && $_POST['param_ids']!=''){
		$param_ids =  json_decode( stripslashes( $_POST['param_ids'] ), true )!='' ? json_decode( stripslashes( $_POST['param_ids'] ), true ) : explode(",",$_POST['param_ids']);
	}
	$end_it_nb ='';
	if($page!=''){ 
		$param_query['paged'] = $page;
		$count_check = $page*$posts_per_page;
		if(($count_check > $count) && (($count_check - $count)< $posts_per_page)){$end_it_nb = $count - (($page - 1)*$posts_per_page);}
		else if(($count_check > $count)) {die;}
	}
	global $wpdb;
	$param_query['post__in'] ='';
	if(isset($_POST['cat']) && $_POST['cat']!=''){
		$texo = array(
			array(
				'taxonomy' => 'exfood_cat',
				'field'    => 'slug',
				'terms'    => $_POST['cat'],
			),
		);
	}else{
		$param_query['tax_query'] ='';
		if($cat!=''){
			$taxonomy ='exfood_cat'; 
			$cats = explode(",",$cat);
			if(is_numeric($cats[0])){$field = 'term_id'; }else{ $field = 'slug'; }
			if(count($cats)>1){
				  $texo = array( 'relation' => 'OR');
				  foreach($cats as $iterm) {
					  $texo[] = array(
							  'taxonomy' => $taxonomy,
							  'field' => $field,
							  'terms' => $iterm,
						  );
				  }
			  }else{
				  $texo = array(
					  array(
							  'taxonomy' => $taxonomy,
							  'field' => $field,
							  'terms' => $cats,
						  )
				  );
			}
			
		}
	}
	if ( exfood_get_option('exfood_enable_loc') =='yes' ) {
		$loc = isset($_SESSION['ex_userloc']) && $_SESSION['ex_userloc']!='' ? $_SESSION['ex_userloc'] :'';
		if($loc!=''){
			$loc = explode(",",$loc);
			if(is_numeric($loc[0])){$field = 'term_id'; }
			else{ $field = 'slug'; }
			if(!isset($texo) || !is_array($texo)){ $texo = array();}
			$texo['relation'] = 'AND';
			if(count($loc)>1){
				  foreach($loc as $iterm) {
					  $texo[] = 
						  array(
							  'taxonomy' => 'exfood_loc',
							  'field' => $field,
							  'terms' => $iterm,
						  );
				  }
			  }else{
				  $texo[] = 
					  array(
							  'taxonomy' => 'exfood_loc',
							  'field' => $field,
							  'terms' => $loc,
				  );
			}
		}
	}
	if(isset($texo)){
		$param_query['tax_query'] = $texo;
	}
	
	$the_query = new WP_Query( $param_query );
	$it = $the_query->post_count;
	ob_start();
	if($the_query->have_posts()){
		$it = $the_query->found_posts;
		if($it < $count || $count=='-1'){ $count = $it;}
		if($count  > $posts_per_page){
			$num_pg = ceil($count/$posts_per_page);
			$it_ep  = $count%$posts_per_page;
		}else{
			$num_pg = 1;
		}
		$arr_ids = array();
		$html_modal = '';
		$i = 0;
		while($the_query->have_posts()){ $the_query->the_post();
			$i++;
			if($layout=='list'){
				echo '<div class="fditem-list item-grid" data-id="ex_id-'.esc_attr($ID).'-'.get_the_ID().'" data-id_food="'.get_the_ID().'"  name="SearchItemElements" data-name="SearchResultsItem" > ';
						?>
					<div class="exp-arrow" >
						<?php 
						exfood_template_plugin('list-'.$style,1);
						?>
					<div class="exfd_clearfix"></div>
					</div>
					<?php

                echo ' <h4 class="ml-4" style="height: 2px">&nbsp;<span class="badge bg-black fg-white" style="margin-top:20px; margin-right:20px; " id="productIDLoopBadge';
                echo get_the_ID() ; #the_ID();
                echo '" name="productBadge" >0</span></h4>';

				echo '</div>';
			}elseif($layout=='table'){
				exfood_template_plugin('table-'.$style,1);
			}else{
				echo '<div class="item-grid" data-id="ex_id-'.esc_attr($ID).'-'.get_the_ID().'" data-id_food="'.get_the_ID().'"  name="SearchItemElements" data-name="SearchResultsItem" > ';
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
			if($end_it_nb!='' && $end_it_nb == $i){break;}
		}
		
		wp_reset_postdata();
		
		?>
        </div>
        <?php
	}

	$html = ob_get_contents();
	ob_end_clean();
	if($html==''){
		$html = esc_html__('No matching records found','wp-food');
	}
	ob_start();
	global $modal_html;
		if(!isset($modal_html) || $modal_html!='on'){
			$modal_html = 'on';
			echo "<div id='food_modal' class='ex_modal'></div>";
		}
	if($page_navi=='loadmore'){
		exfood_ajax_navigate_html($ID,$atts,$num_pg,$param_query,$arr_ids); 
	}else{
		exfood_page_number_html($the_query,$ID,$atts,$num_pg,$param_query,$arr_ids);
	}
	$page_navihtml = ob_get_contents();
	ob_end_clean();
	$output =  array('html_content'=>$html,'page_navi'=> $page_navihtml,'html_modal'=>$html_modal);
	echo str_replace('\/', '/', json_encode($output));
	die;
}
if(!function_exists('exfood_search_form_html')){
	function exfood_search_form_html($cats, $pos = false){
		$args = array(
			'hide_empty'        => true,
		);
		$cats = $cats!=''? explode(",",$cats) : array();
		if (!empty($cats) && !is_numeric($cats[0])) {
			$args['slug'] = $cats;
		}else if (!empty($cats)) {
			$args['include'] = $cats;
		}
		$terms = get_terms('exfood_cat', $args);
		?>
        <div class="exfd-filter">
	    	<div class="exfd-filter-group">
	            <?php if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){ 
	            	$select_option = $list_item = '';
	            	
	            	?>
	            	<div class="ex-menu-list">
	            		<?php if (isset($pos) && $pos=='left'){
	            			$act_cls = 'ex-active-left';
	            		}else{
	            			$act_cls = 'ex-menu-item-active';
	            		}?>
	            		<a class="ex-menu-item <?php esc_attr_e($act_cls);?>" href="javascript:;"><?php echo esc_html__('All','wp-food'); ?></a><?php 
	            			foreach ( $terms as $term ) {
						  		echo '<a class="ex-menu-item" href="'.get_term_link( $term ).'" data-value="'. esc_attr($term->slug) .'">'. wp_kses_post($term->name) .'</a>';
						  	}
	            			?>
	            		<div class="exfd_clearfix"></div>
	            	</div>
	            	<div class="ex-menu-select">
		                <select name="exfood_cat">
		                	<option value=""><?php echo esc_html__('All','wp-food'); ?></option>
		                	<?php  
	            			foreach ( $terms as $term ) {
						  		echo '<option value="'. esc_attr($term->slug) .'">'. wp_kses_post($term->name) .'</option>';
						  	}
		                	?>
		                </select>
		            </div>
	            <?php } //if have terms ?>
	        </div>
        </div>
        <?php
	}
}
function exfood_convert_color($color){
	if ($color == '') {
		return;
	}
	$hex  = str_replace("#", "", $color);
	if(strlen($hex) == 3) {
	  $r = hexdec(substr($hex,0,1).substr($hex,0,1));
	  $g = hexdec(substr($hex,1,1).substr($hex,1,1));
	  $b = hexdec(substr($hex,2,1).substr($hex,2,1));
	} else {
	  $r = hexdec(substr($hex,0,2));
	  $g = hexdec(substr($hex,2,2));
	  $b = hexdec(substr($hex,4,2));
	}
	$rgb = $r.','. $g.','.$b;
	return $rgb;
}

if(!function_exists('exfood_sale_badge')){
	function exfood_sale_badge($sale_price){
		if($sale_price >0 ){ ?>
			<div class="exfd-ribbon"><span>Sale</span></div>
		<?php }
	}
}
/**
* Show add to cart form
*
* @param array $atts Attributes.
* @return string
*/
function exfood_build_in_cart_form($hide_pm){
	global $attr,$id_food;
	?>
	<div class="exfood-buildin-cart">
		<form class="exform" method="post" action="<?php esc_url(home_url())?>">
			<input type="hidden" name="food_id" value="<?php echo esc_attr($id_food); ?>">
			<?php
			$options = get_post_meta( $id_food, 'exfood_addition_data', true );
			if(!empty($options)){
				$i = 0;
				foreach ($options as $item) {
					$type = isset($item['_type']) && $item['_type']!='' ? $item['_type'] : 'checkbox';
					$required = isset($item['_required']) && $item['_required']!='' ? 'ex-required' : '';
					echo '<div class="exrow-group ex-'.esc_attr($type).' '.esc_attr($required).'">';
					if($item['_name']){
						echo  '<span class="exfood-label">'.$item['_name'].'</span>' ;
					}
					if(isset($item['_type']) && $item['_type'] =='radio'){
						if(isset($item['_value'] ) && (!empty($item['_value'])) ){
							foreach ($item['_value'] as $key => $value) {
								$data_option = explode("|",$value);
								$op_name = isset($data_option[0])? $data_option[0] : '';
								$op_val = isset($data_option[1])? $data_option[1] : '';

								$op_name = $op_val !='' ? $op_name .' + '.exfood_price_with_currency($op_val) : $op_name;
								echo '<span><input class="ex-options" type="radio" name="ex_extrafood_'.esc_attr($i).'[]" value="'.esc_attr($key).'|'.esc_attr($value).'" data-price="'.esc_attr($op_val).'">'.wp_kses_post($op_name).'</span>';
							}
						}
					}else if(isset($item['_type']) && $item['_type'] =='select'){
						if(isset($item['_value'] ) && (!empty($item['_value'])) ){
							echo '<select class="ex-options" name="ex_extrafood_'.esc_attr($i).'[]">';
							echo '<option value="" data-price="">'.esc_html__( 'Select', 'wp-food' ).'</option>';
							foreach ($item['_value'] as $key => $value) {
								$data_option = explode("|",$value);
								$op_name = isset($data_option[0])? $data_option[0] : '';
								$op_val = isset($data_option[1])? $data_option[1] : '';

								$op_name = $op_val !='' ? $op_name .' + '.exfood_price_with_currency($op_val) : $op_name;
								echo '<option value="'.esc_attr($key).'|'.esc_attr($value).'" data-price="'.esc_attr($op_val).'">'.esc_attr($op_name).'</option>';
							}
							echo '<select>';
						}
					}else{
						if(isset($item['_value'] ) && (!empty($item['_value'])) ){
							foreach ($item['_value'] as $key => $value) {
								$data_option = explode("|",$value);
								$op_name = isset($data_option[0])? $data_option[0] : '';
								$op_val = isset($data_option[1])? $data_option[1] : '';

								$op_name = $op_val !='' ? $op_name .' + '.exfood_price_with_currency($op_val) : $op_name;
								echo '<span><input class="ex-options" type="checkbox" name="ex_extrafood_'.esc_attr($i).'[]" value="'.esc_attr($key).'|'.esc_attr($value).'" data-price="'.esc_attr($op_val).'">'.esc_attr($op_name).'</span>';
							}
						}
					}
					if($required!=''){
						echo '<p class="ex-required-message">'.esc_html__('This option is required', 'wp-food' ).'</p>';
					}
					echo '</div>';
					$i ++;
					//print_r($item);
				}
			}
			?>
			<div class="exfood-sm">
				<div class="exfood-quantity">
					<?php if($hide_pm!='1'){?>
						<input type="button" class="minus_food" value="-">
						<input type="number" min="1" name="food_qty" class="food_qty" value="1">
						<input type="button" class="plus_food" value="+">
					<?php }else{?>
						<input type="hidden" min="1" name="food_qty" class="food_qty" value="1">
					<?php }?>
				</div>
				<button class="exstyle-button-bin ex-cart">
					<span class="ex-order"><?php esc_html_e( 'Order', 'wp-food' );?></span>
					<span class="ex-added exhidden"><?php esc_html_e( 'Added to cart', 'wp-food' );?></span>
				</button>
			</div>
		</form>
	</div>
	<?php
}
if(!function_exists('exfood_add_to_cart_form_shortcode')){
	function exfood_add_to_cart_form_shortcode( $atts ) {
		$hide_pm = isset( $atts['hide_pm']) ? $atts['hide_pm'] : '';
		if ( exfood_get_option('exfood_booking') !='woo' ) {
			ob_start();
			exfood_build_in_cart_form($hide_pm);
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}
		if ( empty( $atts ) || !function_exists('woocommerce_template_single_add_to_cart')) { return '';}
		if ( ! isset( $atts['id'] ) && ! isset( $atts['sku'] ) ) { return '';}
		$args = array(
			'posts_per_page'      => 1,
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'no_found_rows'       => 1,
		);
		if ( isset( $atts['sku'] ) ) {
			$args['meta_query'][] = array(
				'key'     => '_sku',
				'value'   => sanitize_text_field( $atts['sku'] ),
				'compare' => '=',
			);
			$args['post_type'] = array( 'product', 'product_variation' );
		}
		if ( isset( $atts['id'] ) ) {
			$args['p'] = absint( $atts['id'] );
		}
		// Change form action to avoid redirect.
		add_filter( 'woocommerce_add_to_cart_form_action', '__return_empty_string' );
		$single_product = new WP_Query( $args );
		$preselected_id = '0';
		// Check if sku is a variation.
		if ( isset( $atts['sku'] ) && $single_product->have_posts() && 'product_variation' === $single_product->post->post_type ) {
			$variation = new WC_Product_Variation( $single_product->post->ID );
			$attributes = $variation->get_attributes();
			// Set preselected id to be used by JS to provide context.
			$preselected_id = $single_product->post->ID;
			// Get the parent product object.
			$args = array(
				'posts_per_page'      => 1,
				'post_type'           => 'product',
				'post_status'         => 'publish',
				'ignore_sticky_posts' => 1,
				'no_found_rows'       => 1,
				'p'                   => $single_product->post->post_parent,
			);
			$single_product = new WP_Query( $args );
			?>
			<script type="text/javascript">
				jQuery( document ).ready( function( $ ) {
					var $variations_form = $( '[data-product-page-preselected-id="<?php echo esc_attr( $preselected_id ); ?>"]' ).find( 'form.variations_form' );
					<?php foreach ( $attributes as $attr => $value ) { ?>
						$variations_form.find( 'select[name="<?php echo esc_attr( $attr ); ?>"]' ).val( '<?php echo esc_js( $value ); ?>' );
					<?php } ?>
				});
			</script>
		<?php
		}
		// For "is_single" to always make load comments_template() for reviews.
		$single_product->is_single = false;
		ob_start();
		global $wp_query;
		// Backup query object so following loops think this is a product page.
		$previous_wp_query = $wp_query;
		$wp_query          = $single_product;
		wp_enqueue_script( 'wc-single-product' );
		while ( $single_product->have_posts() ) {
			$single_product->the_post();?>
			<div class="single-product" data-product-page-preselected-id="<?php echo esc_attr( $preselected_id ); ?>">
				<?php woocommerce_template_single_add_to_cart(); 
				if($hide_pm!='1'){?>
					<script type="text/javascript">
						jQuery(document).ready(function() {
							jQuery( '#food_modal .exfood-woocommerce .cart div.quantity:not(.buttons_added)' ).addClass( 'buttons_added' ).append( '<input type="button" value="+" id="add_ticket" class="plus" />' ).prepend( '<input type="button" value="-" id="minus_ticket" class="minus" />' );
							jQuery('.exfood-woocommerce .buttons_added').on('click', '#minus_ticket',function() {
								var value = parseInt(jQuery(this).closest(".quantity").find('.qty').val()) - 1;
								if(value>0){
									jQuery(this).closest(".quantity").find('.qty').val(value);
								}
							});
							jQuery('.exfood-woocommerce .buttons_added').on('click', '#add_ticket',function() {
								var value = parseInt(jQuery(this).closest(".quantity").find('.qty').val()) + 1;
								jQuery(this).closest(".quantity").find('.qty').val(value);
							});
						});
					</script>
				<?php 
				}?>
			</div>
			<?php
		}
		// Restore $previous_wp_query and reset post data.
		$wp_query = $previous_wp_query;
		wp_reset_postdata();
		return '<div class="exfood-woocommerce woocommerce">' . ob_get_clean() . '</div>';
	}
}
add_shortcode( 'ex_food_wooform', 'exfood_add_to_cart_form_shortcode' );

add_action( 'wp_ajax_exfood_booking_info', 'ajax_exfood_booking_info' );
add_action( 'wp_ajax_nopriv_exfood_booking_info', 'ajax_exfood_booking_info' );

function ajax_exfood_booking_info(){
	if(isset($_POST['id_food']) && $_POST['id_food']!=''){
		$product_exist = get_post_meta( $_POST['id_food'], 'exfood_product', true );
		global $atts,$id_food;
		$id_food = $_POST['id_food'];
        if($product_exist!='' && is_numeric($product_exist)){
			$atts['id'] = $product_exist;
		}
		exfood_template_plugin('modal',true);
	}else{
		echo 'error';
	}
	exit;	
}

add_action('wp_ajax_exfood_add_to_cart', 'exfood_ajax_add_to_cart');
add_action('wp_ajax_nopriv_exfood_add_to_cart', 'exfood_ajax_add_to_cart');
function exfood_ajax_add_to_cart() {
    WC_AJAX::get_refreshed_fragments();
    wp_die();
}

/*--- Booking button ---*/
if(!function_exists('exfood_booking_button_html')){
	function exfood_booking_button_html($style) {
		$html = '<a href="'.get_the_permalink(get_the_ID()).'" class="exstyle-'.esc_attr($style).'-button">'.esc_html__( 'Order', 'wp-food' ).'</a>';
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$product_exist = get_post_meta( get_the_ID(), 'exfood_product', true );
        if(exfood_get_option('exfood_booking') =='woo' && is_plugin_active( 'woocommerce/woocommerce.php' ) &&$product_exist!='' && is_numeric($product_exist)){
        	$product = wc_get_product ($product_exist);
        	if($product!==false) {
	        	$type = $product->get_type();
				if (is_plugin_active( 'woocommerce-product-addons/woocommerce-product-addons.php' ) ) {
					$product_addons = get_product_addons( $product_exist, false );
					if ( is_array( $product_addons ) && sizeof( $product_addons ) > 0 ) {
					}else if($type =='simple'){
						$html = do_shortcode( '[ex_food_wooform id="'.$product_exist.'" hide_pm="1"]');
					}
				}else if($type =='simple'){
					$html = do_shortcode( '[ex_food_wooform id="'.$product_exist.'" hide_pm="1"]');
				}
			}
		}else if(exfood_get_option('exfood_booking') !='woo'){
			$options = get_post_meta( get_the_ID(), 'exfood_addition_data', true );
			if(empty($options)){
				global $id_food;
				$id_food = get_the_ID();
				$html = do_shortcode( '[ex_food_wooform id="" hide_pm="1"]');
			}
		}
		//inline button
		echo '<div class="exbt-inline">'.$html.'</div>';
		
	}
}

add_filter( 'woocommerce_add_to_cart_fragments', 'exfood_woo_cart_count_fragments', 10, 1 );
function exfood_woo_cart_count_fragments( $fragments ) {
    $fragments['span.exfd-cart-num'] = '<span class="exfd-cart-num">' . WC()->cart->get_cart_contents_count() . '</span>';
    
    return $fragments;
}

add_filter( 'woocommerce_add_to_cart_fragments', 'exfood_woo_cart_content_fragments', 10, 1 );
function exfood_woo_cart_content_fragments( $fragments ) {
    ob_start();?>
    <div class="exfd-cart-mini"><?php woocommerce_mini_cart(); ?></div>
    <?php
    $fragments['div.exfd-cart-mini'] = ob_get_clean();
    return $fragments;
}
function register_exfood_session()
{
  if( !session_id() )
  {
    session_start();
  }
  $user_ID= get_current_user_id(); 
  $_SESSION['ex_userid'] = $user_ID;
}
add_action('init', 'register_exfood_session');
// exfood price
function exfood_price_with_currency($price){
	if($price=='' || !is_numeric($price)){ return;}
	$num_decimal = exfood_get_option('exfood_num_decimal');
	$decimal_sep = exfood_get_option('exfood_decimal_sep');
	$thousand_sep = exfood_get_option('exfood_thousand_sep');
	if ($num_decimal > 0) {
	    $price = number_format((float)$price, $num_decimal, $decimal_sep, $thousand_sep);
	}
	$currency = exfood_get_option('exfood_currency');
	if ($currency=='') {
		$currency ='$';
	}
	$position = exfood_get_option('exfood_position');
	if($position==0){
		$price = $price.$currency;
	}else{
		$price = $currency.$price;
	}
	return $price;
}
function exfood_woo_cart_icon_html(){
	global $cart_icon;
	if(!isset($cart_icon) || $cart_icon!='on'){
		$cart_icon = 'on';
	}else if($cart_icon =='on'){
		return;
	}
	if(is_admin() || exfood_get_option('exfood_booking') =='woo' && !function_exists('wc_get_cart_url')){ return;}
	?>
	<div class="exfd-shopping-cart">
    	<div class="exfd-cart-parent">
    		<a href="javascript:;">
				<img src="<?php echo EXFOOD_PATH.'css/exfdcart2.svg';?>" alt="image">
				<?php if ( exfood_get_option('exfood_booking') !='woo' ) { ?>
					<span class="exfd-cart-count"><?php echo !empty($_SESSION['ex_userfood']) ? wp_kses_post(count($_SESSION['ex_userfood'])) : 0; ?></span>
				<?php }else{?>
					<span class="exfd-cart-num"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
				<?php }?>
			</a>
		</div>
    </div>
    <div class="exfd-overlay"></div>
    <div class="exfd-cart-content">
    	<span class="exfd-close-cart">&times;</span>
    	<?php exfood_template_plugin('cart-mini',1);?>
	</div>
	<?php
}
function exfood_cart_shortcode(){
	ob_start();?>
	<div class="exfood-cart-shortcode exfd-cart-content exfood-buildin-cart">
    	<?php exfood_template_plugin('cart',1);?>
	</div>
	<?php
	$cart_content = ob_get_contents();
	ob_end_clean();
	return $cart_content;
}

add_shortcode( 'exfood_cart', 'exfood_cart_shortcode' );

add_action( 'wp_ajax_exfood_add_cart_item', 'ajax_exfood_add_cart_item' );
add_action( 'wp_ajax_nopriv_exfood_add_cart_item', 'ajax_exfood_add_cart_item' );

function ajax_exfood_add_cart_item(){
	$data_food = array();
	parse_str($_POST['data'], $data_food);
	if (!$_SESSION['ex_userfood'] || $_SESSION['ex_userfood']=='' || !is_array($_SESSION['ex_userfood'])) {
		$_SESSION['ex_userfood'] = array();
	}
	$_SESSION['ex_userfood'][] = $data_food;
	ob_start();
	exfood_template_plugin('cart-mini',1);
	$cart_update = ob_get_contents();
	ob_end_clean();
	$output =  array('status'=>1,'cart_content'=> $cart_update);
	echo str_replace('\/', '/', json_encode($output));
	exit;	
}

add_action( 'wp_ajax_exfood_remove_cart_item', 'ajax_exfood_remove_cart_item' );
add_action( 'wp_ajax_nopriv_exfood_remove_cart_item', 'ajax_exfood_remove_cart_item' );

function ajax_exfood_remove_cart_item(){

	$key = $_POST['it_remove'];
	if(is_numeric($key)){
		unset($_SESSION['ex_userfood'][$key]);
		$avari = 1;
	}else{
		$avari = 0;
	}
	$total_price = exfood_update_total_price($_SESSION['ex_userfood']);
	$mes = '';
	if(empty($_SESSION['ex_userfood'])){
		$mes = '<div class="exfood-warning">'.esc_html__('Your cart is currently empty.','wp-food').'</div>';
	}
	$output =  array('status'=>$avari,'update_total'=> $total_price, 'message'=> $mes);
	echo str_replace('\/', '/', json_encode($output));
	exit;	
}

function exfood_update_total_price($data){
	$total_price = 0;
 	foreach ($data as $key => $value) {
 		$food_id = $value['food_id'];
 		$price_food = get_post_meta( $food_id, 'exfood_price', true );
 		$saleprice = get_post_meta( $food_id, 'exfood_sale_price', true );
    	$price_food = $saleprice!='' && is_numeric($saleprice) ? $saleprice : $price_food;
    	$price_food = is_numeric($price_food) ? $price_food : 0;
    	foreach ($value as $key_it => $item_meta) {
    		if(is_array($item_meta)){
				foreach ($item_meta as $val) {
					$val = explode("|",$val);
					$price = isset ($val[2]) ? $val[2] : 0;
					$price_food = $price_food + $price*1;
				}
			}
    	}

    	$total_price = $total_price + $price_food * $value['food_qty'];
 	}
 	return exfood_price_with_currency($total_price);
}

add_action( 'wp_ajax_exfood_update_cart_item', 'ajax_exfood_update_cart_item' );
add_action( 'wp_ajax_nopriv_exfood_update_cart_item', 'ajax_exfood_update_cart_item' );

function ajax_exfood_update_cart_item(){
	session_start();
	$key = $_POST['it_update'];
	$qty = $_POST['qty'];
	if(is_numeric($key) && isset($_SESSION['ex_userfood'][$key])){
		$_SESSION['ex_userfood'][$key]['food_qty'] = $qty;
		$avari = 1;
	}else{
		$avari = 0;
		$number_item = count($_SESSION['ex_userfood']);
		$output =  array('status'=>$avari,'info_text'=> esc_html__("This item does not exist in cart","wp-food"),'number_item'=>$number_item);
		echo str_replace('\/', '/', json_encode($output));
		exit;
	}
	$total_price = exfood_update_total_price($_SESSION['ex_userfood']);

	$food_id = $_SESSION['ex_userfood'][$key]['food_id'];
	$price_food = get_post_meta( $food_id, 'exfood_price', true );
	$saleprice = get_post_meta( $food_id, 'exfood_sale_price', true );
	$price_food = $saleprice!='' && is_numeric($saleprice) ? $saleprice : $price_food;
	$price_food = is_numeric($price_food) ? $price_food : 0;
	foreach ($_SESSION['ex_userfood'][$key] as $key_it => $item_meta) {
		if(is_array($item_meta)){
			foreach ($item_meta as $val) {
				$val = explode("|",$val);
				$price = isset ($val[2]) ? $val[2] : 0;
				$price_food = $price_food + $price*1;
			}
		}
	}

	$total_price = $price_food * $_SESSION['ex_userfood'][$key]['food_qty'];
	$total_cart = exfood_update_total_price($_SESSION['ex_userfood']);
	$output =  array('status'=>$avari,'update_price'=> exfood_price_with_currency($total_price),'update_total'=> $total_cart);
	echo str_replace('\/', '/', json_encode($output));
	exit;	
}

function exfood_checkout_shortcode(){
	ob_start();?>
	<div class="exfood-checkout-shortcode">
    	<?php exfood_template_plugin('checkout',1);?>
	</div>
	<?php
	$cart_content = ob_get_contents();
	ob_end_clean();
	return $cart_content;
}

add_shortcode( 'exfood_checkout', 'exfood_checkout_shortcode' );

function exfood_checkemail($email) {
	if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
		return true;
	}else{
		return false;
	}
}
function exfood_select_loc_html($atts){
	$locations = isset($atts['locations']) ? $atts['locations'] : '';
	$args = array(
		'hide_empty'        => true,
	);
	$locations = $locations!='' ? explode(",",$locations) : array();
	if (!empty($locations) && !is_numeric($locations[0])) {
		$args['slug'] = $locations;
	}else if (!empty($locations)) {
		$args['include'] = $locations;
	}
	$terms = get_terms('exfood_loc', $args);
	ob_start();
	$loc_selected = isset($_SESSION['ex_userloc']) && $_SESSION['ex_userloc']!='' ? $_SESSION['ex_userloc'] :'';
	?>
	<div class="exfood-select-loc">
		<div>
			<select class="ex-loc-select">
				<?php if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
					global $wp;
					$cr_url =  home_url( $wp->request );
		        	$select_option = '';
		        	echo  '<option disabled selected value>'.esc_html__( '-- Select --', 'wp-food' ) .'</option>';
		        	foreach ( $terms as $term ) {
		        		$url = add_query_arg(array('loc' => $term->slug), $cr_url);
		        		$selected = $loc_selected == $term->slug ? 'selected' : '';
				  		echo '<option value="'. esc_url($url) .'" '.esc_attr($selected).' >'. wp_kses_post($term->name) .'</option>';
				  	}
		        } //if have terms ?>
			</select>
		</div>
	</div>
	<?php
	$cart_content = ob_get_contents();
	ob_end_clean();
	return $cart_content;
}
add_shortcode( 'exfood_sllocation', 'exfood_select_loc_html' );
function exfood_select_location_html($locations){
	if ( exfood_get_option('exfood_enable_loc') !='yes' ) {
		return;
	}
	global $loc_exits;
	$loc_selected = isset($_SESSION['ex_userloc']) && $_SESSION['ex_userloc']!='' ? $_SESSION['ex_userloc'] :'';
	if($loc_selected!=''){
		return;
	}
	if(!isset($loc_exits) || $loc_exits!='on'){
		$loc_exits = 'on';
	}else if($loc_exits =='on'){
		return;
	}
	$atts = array();
	$atts['locations'] = $locations;
	?>
	<div class="ex-popup-location">
		<div class="ex-popup-content">
			<?php
			$icon = exfood_get_option('exfood_loc_icon');
			if($icon!=''){ ?>
				<div class="ex-pop-icon">
					<img src="<?php echo esc_url($icon);?>" alt="image">
				</div>
			<?php } ?>
			<div class="ex-popup-info">
				<h1><?php esc_html_e('Please choose area you want to order','wp-food');?></h1>
				<?php echo exfood_select_loc_html($atts); ?>
			</div>
		</div>
	
	</div>
	<?php
}
add_action( 'init', 'exfood_user_select_location',20 );
function exfood_user_select_location(){
	if(isset($_GET["loc"]) && $_GET["loc"]!=''){
		$term = term_exists( $_GET["loc"], 'exfood_loc' );
		if ( $term !== 0 && $term !== null ) {
			$_SESSION['ex_userloc'] = $_GET["loc"];
		}
	}
}

function exfood_location_field_html(){
	$args = array(
		'hide_empty'        => true,
	);
	$terms = get_terms('exfood_loc', $args);
	ob_start();
	$loc_selected = isset($_SESSION['ex_userloc']) && $_SESSION['ex_userloc']!='' ? $_SESSION['ex_userloc'] :'';
	?>
	<select class="ex-ck-select exfd-choice-locate" name="_location">
		<?php if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
			global $wp;
			if ( exfood_get_option('exfood_enable_loc') !='yes' ) {
	        	$select_option = '';
	        	echo '<option disabled selected value>'.esc_html__( '-- Select --', 'wp-food' ) .'</option>';
	        	foreach ( $terms as $term ) {
			  		echo '<option value="'. esc_attr($term->slug) .'" >'. wp_kses_post($term->name) .'</option>';
			  	}
			}else{
				$term = get_term_by('slug', $loc_selected, 'exfood_loc');
				echo '<option selected value="'.esc_attr( $loc_selected ).'">'.wp_kses_post($term->name).'</option>';
			}
        } //if have terms ?>
	</select>
	<?php
	$loca = ob_get_contents();
	ob_end_clean();
	return $loca;
}

add_action( 'wp_ajax_exfood_loadstore', 'ajax_exfood_loadstore' );
add_action( 'wp_ajax_nopriv_exfood_loadstore', 'ajax_exfood_loadstore' );
function ajax_exfood_loadstore(){
	
	$param_query = json_decode( stripslashes( $_POST['param_query'] ), true );
	$locate_param = '';
	$locate_param = sanitize_text_field($_POST['locate_param']);
	if ($locate_param == '') {
		return;
	}
	ob_start();
	$posts_array = get_posts(
        array(
            'post_status' => array( 'publish'),
            'post_type' => 'exfood_store',
            'tax_query' => array(
                array(
                    'taxonomy' => 'exfood_loc',
                    'field' => 'slug',
                    'terms' => $locate_param,
                )
            )
        )
    );
    
    $count =sizeof($posts_array);
    if ($count == 0) {
    	echo "0";
    	// return;
    }else{
	    echo '<label class="exfd-label">'.esc_html__("Select store","wp-food").'</label>';
	    $number = 1;
	    $check='';
		foreach ( $posts_array as $it ) {
			if ($number == 1) {
				$check ='checked="checked"';
			}else{$check ='';}
			$number = $number + 1;
			echo '<label class="exfd-container"><p>'.$it->post_title.'</p>
				<span>'.wpautop($it->post_content).'</span>
				<input class="exfd-choice-order" type="radio" name="_store" '.$check.' value="'.$it->ID.'">
				<span class="exfd-checkmark"></span>
	        </label>';
		}
	}
	// echo "</ul>";
	$html = ob_get_clean();
	$output =  array('html_content'=>$html);
	echo str_replace('\/', '/', json_encode($output));
	die;
}


if(!function_exists('exfood_pagenavi_no_ajax')){
	function exfood_pagenavi_no_ajax($the_query){
		if(function_exists('paginate_links')) {
			echo '<div class="exfood-no-ajax-pagination">';
			echo paginate_links( array(
				'base'         => esc_url_raw( str_replace( 999999999, '%#%', get_pagenum_link( 999999999, false ) ) ),
				'format'       => '',
				'add_args'     => false,
				'current' => max( 1, get_query_var('paged') ),
				'total' => $the_query->max_num_pages,
				'prev_text'    => '&larr;',
				'next_text'    => '&rarr;',
				'type'         => 'list',
				'end_size'     => 3,
				'mid_size'     => 3
			) );
			echo '</div>
			<style type="text/css">
				.exfood-no-ajax-pagination{ margin-top:60px;}
				.exfood-no-ajax-pagination ul{text-align: center;}
				.exfood-no-ajax-pagination ul li{ list-style:none; width:auto; display: inline-block;}
				.exfood-no-ajax-pagination ul li a,
				.exfood-no-ajax-pagination ul li span{
					display: inline-block;
					background: none;
					background-color: #FFFFFF;
					padding: 5px 15px 0 15px;
					color: rgba(153,153,153,1.0);
					margin: 0px 10px 10px 0;
					min-width: 40px;
					min-height: 40px;
					text-align: center;
					text-decoration: none;
					vertical-align: top;
					font-size: 16px;
					border-radius: 0px;
					box-shadow: 0 0 1px rgba(0, 0, 0, 0.15);
					transition: all .2s;
					border: 1px solid rgba(0, 0, 0, 0.15);
					line-height: 1.7;
				}
				.exfood-no-ajax-pagination ul li a:hover,
				.exfood-no-ajax-pagination ul li span.current{ color: rgba(119,119,119,1.0); background-color: rgba(238,238,238,1.0);}
			}
			</style>';
		}
	}
}