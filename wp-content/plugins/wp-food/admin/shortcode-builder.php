<?php
class exfood_SC_Builder {
	public function __construct(){
        add_action( 'init', array( &$this, 'register_post_type' ) );
		add_action( 'cmb2_admin_init', array(&$this,'register_metabox') );
		add_action( 'save_post', array($this,'save_shortcode'),1 );
		add_shortcode( 'extpsc', array($this,'run_extpsc') );
    }
	function run_extpsc($atts, $content){
		$id = isset($atts['id']) ? $atts['id'] : '';
		$sc = get_post_meta( $id, '_tpsc', true );
		if($id=='' || $sc==''){ return;}
		return do_shortcode($sc);
	}
	function save_shortcode($post_id){
		if('exfood_scbd' != get_post_type()){ return;}
		if(isset($_POST['sc_type'])){
			$style = isset($_POST['style']) ? $_POST['style'] : 1;
			$column = isset($_POST['column']) ? $_POST['column'] : 3;
			$count = isset($_POST['count']) && $_POST['count'] !=''? $_POST['count'] : '9';
			$posts_per_page = isset($_POST['posts_per_page']) ? $_POST['posts_per_page'] : '';
			$slidesshow = isset($_POST['slidesshow']) ? $_POST['slidesshow'] : '';
			$ids = isset($_POST['ids']) ? $_POST['ids'] : '';
			$cat = isset($_POST['cat']) ? $_POST['cat'] : '';
			$order = isset($_POST['order']) ? $_POST['order'] : '';
			$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : '';
			$meta_key = isset($_POST['meta_key']) ? $_POST['meta_key'] : '';
			$meta_value = isset($_POST['meta_value']) ? $_POST['meta_value'] : '';
			$number_excerpt = isset($_POST['number_excerpt']) ? $_POST['number_excerpt'] : '';
			$page_navi = isset($_POST['page_navi']) ? $_POST['page_navi'] : '';
			$cart_enable = isset($_POST['cart_enable']) ? $_POST['cart_enable'] : '';
			$menu_filter = isset($_POST['menu_filter']) ? $_POST['menu_filter'] : '';
			$menu_pos = isset($_POST['menu_pos']) ? $_POST['menu_pos'] : '';
			$live_sort = isset($_POST['live_sort']) ? $_POST['live_sort'] : '';
			$autoplay = isset($_POST['autoplay']) ? $_POST['autoplay'] : '';
			$autoplayspeed = isset($_POST['autoplayspeed']) ? $_POST['autoplayspeed'] : '';
			$loading_effect = isset($_POST['loading_effect']) ? $_POST['loading_effect'] : '';
			$infinite = isset($_POST['infinite']) ? $_POST['infinite'] : '';

			if($_POST['sc_type'] == 'grid'){
				
				$sc = '[ex_food_grid style="'.esc_attr($style).'" column="'.esc_attr($column).'" count="'.esc_attr($count).'" posts_per_page="'.esc_attr($posts_per_page).'" ids="'.esc_attr($ids).'" cat="'.esc_attr($cat).'" order="'.esc_attr($order).'" orderby="'.esc_attr($orderby).'" meta_key="'.esc_attr($meta_key).'" meta_value="'.esc_attr($meta_value).'" number_excerpt="'.esc_attr($number_excerpt).'" cart_enable="'.esc_attr($cart_enable).'" menu_filter="'.esc_attr($menu_filter).'" page_navi="'.esc_attr($page_navi).'"]';
				
			}elseif($_POST['sc_type'] == 'list'){
				$sc = '[ex_food_list style="'.esc_attr($style).'" count="'.esc_attr($count).'" posts_per_page="'.esc_attr($posts_per_page).'" ids="'.esc_attr($ids).'" cat="'.esc_attr($cat).'" order="'.esc_attr($order).'" orderby="'.esc_attr($orderby).'" meta_key="'.esc_attr($meta_key).'" meta_value="'.esc_attr($meta_value).'" number_excerpt="'.esc_attr($number_excerpt).'" cart_enable="'.esc_attr($cart_enable).'" menu_filter="'.esc_attr($menu_filter).'" menu_pos="'.esc_attr($menu_pos).'"  page_navi="'.esc_attr($page_navi).'" page_navi="'.esc_attr($page_navi).'"]';
				
			}elseif($_POST['sc_type'] == 'table'){
				
				$sc = '[ex_food_table style="'.esc_attr($style).'" count="'.esc_attr($count).'" posts_per_page="'.esc_attr($posts_per_page).'" ids="'.esc_attr($ids).'" cat="'.esc_attr($cat).'" order="'.esc_attr($order).'" orderby="'.esc_attr($orderby).'" meta_key="'.esc_attr($meta_key).'" meta_value="'.esc_attr($meta_value).'" number_excerpt="'.esc_attr($number_excerpt).'" cart_enable="'.esc_attr($cart_enable).'" menu_filter="'.esc_attr($menu_filter).'" live_sort="'.esc_attr($live_sort).'"  page_navi="'.esc_attr($page_navi).'"]';
				
			}else{
				
				$sc = '[ex_food_carousel style="'.esc_attr($style).'" count="'.esc_attr($count).'" slidesshow="'.esc_attr($slidesshow).'" ids="'.esc_attr($ids).'" cat="'.esc_attr($cat).'" order="'.esc_attr($order).'" orderby="'.esc_attr($orderby).'" meta_key="'.esc_attr($meta_key).'" meta_value="'.esc_attr($meta_value).'" number_excerpt="'.esc_attr($number_excerpt).'"  autoplay="'.esc_attr($autoplay).'" cart_enable="'.esc_attr($cart_enable).'" autoplayspeed="'.esc_attr($autoplayspeed).'" loading_effect="'.esc_attr($loading_effect).'" infinite="'.esc_attr($infinite).'"]';
				
			}
			if($sc!=''){
				update_post_meta( $post_id, '_tpsc', $sc );
			}
			update_post_meta( $post_id, '_shortcode', '[extpsc id="'.$post_id.'"]' );
		}
	}
	function register_post_type(){
		$labels = array(
			'name'               => esc_html__('Shortcodes','wp-food'),
			'singular_name'      => esc_html__('Shortcodes','wp-food'),
			'add_new'            => esc_html__('Add New Shortcodes','wp-food'),
			'add_new_item'       => esc_html__('Add New Shortcodes','wp-food'),
			'edit_item'          => esc_html__('Edit Shortcodes','wp-food'),
			'new_item'           => esc_html__('New Shortcode','wp-food'),
			'all_items'          => esc_html__('Shortcodes builder','wp-food'),
			'view_item'          => esc_html__('View Shortcodes','wp-food'),
			'search_items'       => esc_html__('Search Shortcodes','wp-food'),
			'not_found'          => esc_html__('No Shortcode found','wp-food'),
			'not_found_in_trash' => esc_html__('No Shortcode found in Trash','wp-food'),
			'parent_item_colon'  => '',
			'menu_name'          => esc_html__('Shortcodes','wp-food')
		);
		$rewrite = false;
		$args = array(  
			'labels' => $labels,  
			'menu_position' => 8, 
			'supports' => array('title','custom-fields'),
			'public'             => false,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => 'edit.php?post_type=ex_food',
			'menu_icon' =>  'dashicons-editor-ul',
			'query_var'          => true,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'rewrite' => $rewrite,
		);  
		register_post_type('exfood_scbd',$args);  
	}
	
	function register_metabox() {
		/**
		 * Sample metabox to demonstrate each field type included
		 */
		$layout = new_cmb2_box( array(
			'id'            => 'sc_shortcode',
			'title'         => esc_html__( 'Shortcode type', 'wp-food' ),
			'object_types'  => array( 'exfood_scbd' ), // Post type
		) );
	
		$layout->add_field( array(
			'name'             => esc_html__( 'Type', 'wp-food' ),
			'desc'             => esc_html__( 'Select type of shortcode', 'wp-food' ),
			'id'               => 'sc_type',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => 'grid',
			'options'          => array(
				'grid' => esc_html__( 'Grid', 'wp-food' ),
				'table'   => esc_html__( 'Table', 'wp-food' ),
				'list'   => esc_html__( 'List', 'wp-food' ),
				'carousel'     => esc_html__( 'Carousel', 'wp-food' ),
			),
		) );
		if(isset($_GET['post']) && is_numeric($_GET['post'])){
			$layout->add_field( array(
				'name'       => esc_html__( 'Shortcode', 'wp-food' ),
				'desc'       => esc_html__( 'Copy this shortcode and paste it into your post, page, or text widget content:', 'wp-food' ),
				'id'         => '_shortcode',
				'type'       => 'text',
				'classes'             => '',
				'attributes'  => array(
					'readonly' => 'readonly',
				),
			) );
		}
		$sc_option = new_cmb2_box( array(
			'id'            => 'sc_option',
			'title'         => esc_html__( 'Shortcode Option', 'wp-food' ),
			'object_types'  => array( 'exfood_scbd' ),
		) );
		
		$sc_option->add_field( array(
			'name'             => esc_html__( 'Style', 'wp-food' ),
			'desc'             => esc_html__( 'Select style of shortcode', 'wp-food' ),
			'id'               => 'style',
			'type'             => 'select',
			'classes'             => 'column-2',
			'show_option_none' => false,
			'default'          => '1',
			'options'          => array(
				'1' => esc_html__('1', 'wp-food'),
				'2' => esc_html__('2', 'wp-food'),
				'3' => esc_html__('3', 'wp-food'),
				'4' => esc_html__('4', 'wp-food'),
			),
		) );
		
		$sc_option->add_field( array(
			'name'             => esc_html__( 'Columns', 'wp-food' ),
			'desc'             => esc_html__( 'Select Columns of shortcode', 'wp-food' ),
			'id'               => 'column',
			'type'             => 'select',
			'classes'             => 'column-2 hide-incarousel hide-intable hide-inlist show-ingrid',
			'show_option_none' => false,
			'default'          => '3',
			'options'          => array(
				'2' => esc_html__( '2 columns', 'wp-food' ),
				'3'   => esc_html__( '3 columns', 'wp-food' ),
				'4'   => esc_html__( '4 columns', 'wp-food' ),
				'5'     => esc_html__( '5 columns', 'wp-food' ),
			),
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Count', 'wp-food' ),
			'desc'       => esc_html__( 'Number of posts', 'wp-food' ),
			'id'         => 'count',
			'type'       => 'text',
			'classes'             => 'column-2',
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Posts per page', 'wp-food' ),
			'desc'       => esc_html__( 'Number items per page', 'wp-food' ),
			'id'         => 'posts_per_page',
			'type'       => 'text',
			'classes'             => 'column-2 hide-incarousel show-intable show-inlist show-ingrid',
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Number items visible', 'wp-food' ),
			'desc'       => esc_html__( 'Enter number', 'wp-food' ),
			'id'         => 'slidesshow',
			'type'       => 'text',
			'classes'             => 'show-incarousel hide-intable hide-inlist hide-ingrid',
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'IDs', 'wp-food' ),
			'desc'       => esc_html__( 'Specify post IDs to retrieve', 'wp-food' ),
			'id'         => 'ids',
			'type'       => 'text',
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Menu', 'wp-food' ),
			'desc'       => esc_html__( 'List of cat ID (or slug), separated by a comma', 'wp-food' ),
			'id'         => 'cat',
			'type'       => 'text',
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Order', 'wp-food' ),
			'desc'       => '',
			'id'         => 'order',
			'type'             => 'select',
			'classes'             => 'column-2',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				'DESC' => esc_html__('DESC', 'wp-food'),
				'ASC'   => esc_html__('ASC', 'wp-food'),
			),
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Order by', 'wp-food' ),
			'desc'       => '',
			'id'         => 'orderby',
			'type'             => 'select',
			'classes'             => 'column-2',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				'date' => esc_html__('Date', 'wp-food'),
				'ID'   => esc_html__('ID', 'wp-food'),
				'author' => esc_html__('Author', 'wp-food'),
				'title'   => esc_html__('Title', 'wp-food'),
				'name' => esc_html__('Name', 'wp-food'),
				'modified'   => esc_html__('Modified', 'wp-food'),
				'parent' => esc_html__('Parent', 'wp-food'),
				'rand'   => esc_html__('Rand', 'wp-food'),
				'menu_order' => esc_html__('Menu order', 'wp-food'),
				'meta_value'   => esc_html__('Meta value', 'wp-food'),
				'meta_value_num' => esc_html__('Meta value num', 'wp-food'),
				'post__in'   => esc_html__('Post__in', 'wp-food'),
				'None'   => esc_html__('None', 'wp-food'),
			),
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Meta key', 'wp-food' ),
			'desc'       => esc_html__( 'Enter meta key to query', 'wp-food' ),
			'id'         => 'meta_key',
			'type'       => 'text',
			'classes'             => 'column-2',
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Meta value', 'wp-food' ),
			'desc'       => esc_html__( 'Enter meta value to query', 'wp-food' ),
			'id'         => 'meta_value',
			'type'       => 'text',
			'classes'             => 'column-2',
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Number of Excerpt', 'wp-food' ),
			'desc'       => esc_html__( 'Enter number', 'wp-food' ),
			'id'         => 'number_excerpt',
			'type'       => 'text',
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Page navi', 'wp-food' ),
			'desc'       => esc_html__( 'Select type of page navigation', 'wp-food' ),
			'id'         => 'page_navi',
			'type'             => 'select',
			'classes'             => 'column-2 hide-incarousel show-intable show-inlist show-ingrid',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				'' => esc_html__('Number', 'wp-food'),
				'loadmore'   => esc_html__('Load more', 'wp-food'),
			),
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Menu filter', 'wp-food' ),
			'desc'       => esc_html__( 'Select show or hide menu filter bar', 'wp-food' ),
			'id'         => 'menu_filter',
			'type'             => 'select',
			'classes'             => 'column-2 hide-incarousel show-intable show-inlist show-ingrid',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				'hide' => esc_html__('Hide', 'wp-food'),
				'show'   => esc_html__('Show', 'wp-food'),
			),
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Menu filter Position', 'wp-food' ),
			'desc'       => esc_html__( 'Select posstion of menu filter', 'wp-food' ),
			'id'         => 'menu_pos',
			'type'             => 'select',
			'classes'             => 'column-2 hide-incarousel hide-intable show-inlist hide-ingrid',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				'top' => esc_html__('Top', 'wp-food'),
				'left'   => esc_html__('Left', 'wp-food'),
			),
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Show Side cart', 'wp-food' ),
			'desc'       => esc_html__( 'Select show or hide side cart', 'wp-food' ),
			'id'         => 'cart_enable',
			'type'             => 'select',
			'classes'             => '',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				'' => esc_html__('Show', 'wp-food'),
				'no'   => esc_html__('Hide', 'wp-food'),
			),
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Live Sort', 'wp-food' ),
			'desc'       => esc_html__( 'Enable Live Sort', 'wp-food' ),
			'id'         => 'live_sort',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				'' => esc_html__('No', 'wp-food'),
				'1'   => esc_html__('Yes', 'wp-food'),
			),
			'classes'             => 'hide-incarousel show-intable hide-inlist hide-ingrid',
		) );
		
		
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Autoplay', 'wp-food' ),
			'desc'       => esc_html__( 'Enable Autoplay', 'wp-food' ),
			'id'         => 'autoplay',
			'type'             => 'select',
			'classes'             => 'column-2 show-incarousel hide-intable hide-inlist hide-ingrid',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				'' => esc_html__('No', 'wp-food'),
				'1'   => esc_html__('Yes', 'wp-food'),
			),
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Autoplay Speed', 'wp-food' ),
			'desc'       => esc_html__( 'Autoplay Speed in milliseconds. Default:3000', 'wp-food' ),
			'id'         => 'autoplayspeed',
			'type'             => 'text',
			'classes'             => 'column-2 show-incarousel hide-intable hide-inlist hide-ingrid',
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Loading effect', 'wp-food' ),
			'desc'       => esc_html__( 'Enable Loading effect', 'wp-food' ),
			'id'         => 'loading_effect',
			'type'             => 'select',
			'classes'             => 'column-2 show-incarousel hide-intable hide-inlist hide-ingrid',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				'' => esc_html__('No', 'wp-food'),
				'1'   => esc_html__('Yes', 'wp-food'),
			),
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Infinite', 'wp-food' ),
			'desc'       => esc_html__( 'Infinite loop sliding ( go to first item when end loop)', 'wp-food' ),
			'id'         => 'infinite',
			'type'             => 'select',
			'classes'             => 'column-2 show-incarousel hide-intable hide-inlist hide-ingrid',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				'' => esc_html__('No', 'wp-food'),
				'yes'   => esc_html__('Yes', 'wp-food'),
			),
		) );
	
	}
}
$exfood_SC_Builder = new exfood_SC_Builder();