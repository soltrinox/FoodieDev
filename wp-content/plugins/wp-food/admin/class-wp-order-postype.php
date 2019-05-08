<?php
class EXfood_Ordering_Posttype {
	public function __construct()
    {
		add_action( 'init', array( &$this, 'register_post_type' ) );
		add_action( 'init', array( &$this, 'register_post_type_store' ) );
		add_action( 'cmb2_admin_init', array( &$this,'exfood_register_metabox') );
		add_filter( 'manage_exfood_order_posts_columns', array( &$this,'_edit_columns'),99 );
		add_action( 'manage_exfood_order_posts_custom_column', array( &$this,'_custom_columns_content'),12);
    }
    function register_post_type(){
		$labels = array(
			'name'               => esc_html__('Order','wp-food'),
			'singular_name'      => esc_html__('Order','wp-food'),
			'add_new'            => esc_html__('Add New Order','wp-food'),
			'add_new_item'       => esc_html__('Add New Order','wp-food'),
			'edit_item'          => esc_html__('Edit Order','wp-food'),
			'new_item'           => esc_html__('New Order','wp-food'),
			'all_items'          => esc_html__('Orders','wp-food'),
			'view_item'          => esc_html__('View Order','wp-food'),
			'search_items'       => esc_html__('Search Order','wp-food'),
			'not_found'          => esc_html__('No Order found','wp-food'),
			'not_found_in_trash' => esc_html__('No Order found in Trash','wp-food'),
			'parent_item_colon'  => '',
			'menu_name'          => esc_html__('Orders','wp-food')
		);
		$rewrite =  array( 'slug' => 'food-order', 'with_front' => false, 'feeds' => true );
		$args = array(  
			'labels' => $labels,  
			'supports' => array('title','custom-fields'),
			'public'             => false,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => 'edit.php?post_type=ex_food',
			'menu_icon' =>  '',
			'query_var'          => true,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 1,
			'rewrite' => $rewrite,
		);  
		register_post_type('exfood_order',$args);  
	}
	function register_post_type_store(){
		$labels = array(
			'name'               => esc_html__('Store','wp-food'),
			'singular_name'      => esc_html__('Store','wp-food'),
			'add_new'            => esc_html__('Add New Store','wp-food'),
			'add_new_item'       => esc_html__('Add New Store','wp-food'),
			'edit_item'          => esc_html__('Edit Store','wp-food'),
			'new_item'           => esc_html__('New Store','wp-food'),
			'all_items'          => esc_html__('Stores','wp-food'),
			'view_item'          => esc_html__('View Store','wp-food'),
			'search_items'       => esc_html__('Search Store','wp-food'),
			'not_found'          => esc_html__('No Store','wp-food'),
			'not_found_in_trash' => esc_html__('No Store in Trash','wp-food'),
			'parent_item_colon'  => '',
			'menu_name'          => esc_html__('Stores','wp-food')
		);
		$args = array(  
			'labels' => $labels,  
			'supports' => array('title','editor','thumbnail','custom-fields'),
			'public'             => false,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => 'edit.php?post_type=ex_food',
			'menu_icon' =>  '',
			'query_var'          => true,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 1,
			'rewrite' => false,
		);  
		register_post_type('exfood_store',$args);  
	}
	// Register metadata
	function exfood_register_metabox() {
		$prefix = 'exorder_';

		/**
		 * Food general info
		 */
		$order_info = new_cmb2_box( array(
			'id'            => $prefix . 'order_meta',
			'title'         => esc_html__( 'Order details', 'wp-food' ),
			'object_types'  => array( 'exfood_order' ),
		) );
		$order_info->add_field( array(
			'name'       => esc_html__( 'Status', 'wp-food' ),
			'desc'       => '',
			'id'         => 'exfood_order_status',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => '',
			'classes'		 => 'column-2',
			'options'          => array(
				'on-hold'   => __( 'On Hold', 'wp-food' ),
				'process' => __( 'Processing', 'wp-food' ),
				'complete' => __( 'Completed', 'wp-food' ),
				'cancel' => __( 'Cancelled', 'wp-food' ),	
			),
		) );
		$order_info->add_field( array(
			'name'       => esc_html__( 'Order type', 'wp-food' ),
			'desc'       => '',
			'id'         => $prefix . 'type',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => '',
			'classes'		 => 'column-2',
			'options'          => array(
				'order-delivery'   => __( 'Order and  wait delivery', 'wp-food' ),
				'order-pick' => __( 'Order and carryout', 'wp-food' ),
			),
		) );
		$order_info->add_field( array(
			'name'       => esc_html__( 'Billing first name', 'wp-food' ),
			'desc'       => '',
			'id'         => $prefix . 'fname',
			'type'       => 'text',
			'classes'		 => 'column-4',
		) );

		$order_info->add_field( array(
			'name'       => esc_html__( 'Billing last name', 'wp-food' ),
			'desc'       => '',
			'id'         => $prefix . 'lname',
			'type'       => 'text',
			'classes'		 => 'column-4',
		) );
		$order_info->add_field( array(
			'name'       => esc_html__( 'Phone number', 'wp-food' ),
			'desc'       => '',
			'id'         => $prefix . 'phone',
			'type'       => 'text',
			'classes'		 => 'column-4',
		) );
		$order_info->add_field( array(
			'name'       => esc_html__( 'Email', 'wp-food' ),
			'desc'       => '',
			'id'         => $prefix . 'email',
			'type'       => 'text',
			'classes'		 => 'column-4',
		) );
		
		$list_item_dates= array();
		$date = strtotime(date('Y-m-d'));
		for ($i = 0 ; $i<= 10; $i ++ ) {
			$date_un = strtotime("+$i day", $date);
			$list_item_dates[$date_un] = date_i18n(get_option('date_format'), $date_un);
		}
		if(isset($_GET['post']) && is_numeric($_GET['post'])){
			$ordered_unix = '';
			$ordered_unix = get_post_meta( $_GET['post'], $prefix . 'date', true );
			if($ordered_unix!='' && is_numeric($ordered_unix)){
				$ordered_date = date_i18n(get_option('date_format'), $ordered_unix);
				if (!in_array($ordered_date, $list_item_dates) && sizeof($list_item_dates)>0) {
					$list_item_dates = array($ordered_unix => $ordered_date) + $list_item_dates;
				}
			}else if($ordered_unix!=''){
				if (!in_array($ordered_unix, $list_item_dates) && sizeof($list_item_dates)>0) {
					$list_item_dates = array($ordered_unix => $ordered_unix) + $list_item_dates;
				}
			}
		}
		$order_info->add_field( array(
			'name'       => esc_html__( 'Date Delivery', 'wp-food' ),
			'desc'       => '',
			'id'         => $prefix .'date',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => '',
			'options'          => $list_item_dates,
			'classes'		 => 'column-3',
		) );
		$array_time = array();
		$array_time = exfood_get_option('exfood_ck_times','exfood_advanced_options');
		if (empty($array_time)) {
		    $order_info->add_field( array(
				'name'       => esc_html__( 'Time Delivery', 'wp-food' ),
				'desc'       => '',
				'id'         => $prefix .'time',
				'type'       => 'text',
					'classes'		 => 'column-3',
				) );
		}else{
			$array_times = array();
			foreach ( $array_time as $it ) {
				$array_times[$it] = $it;
			}
			$order_info->add_field( array(
				'name'       => esc_html__( 'Time Delivery', 'wp-food' ),
				'desc'       => '',
				'id'         => $prefix .'time',
				'type'             => 'select',
				'show_option_none' => false,
				'default'          => '',
				'options'          => $array_times,
				'classes'		 => 'column-3',
				
			) );
		}
		$args = array('hide_empty'        => true,);
		$terms = get_terms('exfood_loc', $args);
		$list_item = array();
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
			foreach ( $terms as $term ) {
				$list_item[$term->slug] = $term->name;
			}
		}
		$order_info->add_field( array(
			'name'       => esc_html__( 'Location', 'wp-food' ),
			'desc'       => '',
			'id'         => $prefix . 'location',
			'type'             => 'select',
			'show_option_none' => true,
			'default'          => '',
			'options'          => $list_item,
			'classes'		 => 'column-3',
		) );
		// $list_item_store = array();
		// $args_store = array(
		// 	'post_type'        => 'exfood_store',
		// 	'post_status'      => 'publish',
		// );
		// $posts_array_store = get_posts( $args_store );
		// foreach ( $posts_array_store as $it ) {
		// 	$list_item_store[$it->ID] = $it->post_title;
		// }
		// $order_info->add_field( array(
		// 	'name'       => esc_html__( 'Store', 'wp-food' ),
		// 	'desc'       => '',
		// 	'id'         => $prefix . 'store',
		// 	'type'             => 'select',
		// 	'show_option_none' => true,
		// 	'default'          => '',
		// 	'options'          => $list_item_store,
		// 	'classes'		 => 'column-4',
		// ) );
		$store_name = esc_html__( 'Select store for ordering', 'wp-food' );
		if(isset($_GET['post']) && is_numeric($_GET['post'])){
			
			$store = get_post_meta( $_GET['post'], $prefix . 'store', true );
			if($store !='' && is_numeric($store)){
				$store_name = get_the_title( $store );
				$edit_link = get_edit_post_link( $store, true );
				// $html_link = '<a href="'.esc_url($edit_link).'">'.esc_html__('Edit store','wp-food').'</a>';
			}
		}
		$order_info->add_field( array(
			'name'        => esc_html__( 'Store', 'wp-food' ),
			'id'          => $prefix . 'store',
			'type'        => 'post_search_text', // This field type
			'post_type'   => 'exfood_store',
			'desc'       => $store_name,
			'select_type' => 'radio',
			'select_behavior' => 'replace',
			'classes'		 => 'column-2',
			// 'after_field'  => $html_link,
		) );
		$order_info->add_field( array(
			'name'       => esc_html__( 'Shiping address', 'wp-food' ),
			'desc'       => '',
			'id'         => $prefix . 'address',
			'type'       => 'text',
			'classes'		 => 'column-2',
		) );
		$order_info->add_field( array(
			'name'       => esc_html__( 'Order note', 'wp-food' ),
			'desc'       => '',
			'id'         => $prefix . 'note',
			'type'       => 'textarea',
			'classes'		 => '',
		) );
		$order_it = new_cmb2_box( array(
			'id'            => $prefix . 'order_item',
			'title'         => esc_html__( 'Order Item', 'wp-food' ),
			'object_types'  => array( 'exfood_order' ),
		) );
		$order_it->add_field( array(
			'name'       => '',
			'desc'       => '',
			'id'         => $prefix . 'table_item',
			'type'       => 'title',
			'classes'		 => '',
			'after_field' => 'exfood_table_prder_item',
		) );
		$order_it->add_field( array(
			'name'       => '',
			'desc'       => '',
			'id'         => $prefix . 'food_id',
			'type'       => 'post_search_text',
			'post_type'   => 'ex_food',
			'select_type' => 'radio',
			'select_behavior' => 'replace',
			'classes'		 => '',
			'before_field' => '<span class="button exfood-add-food dashicons-search cmb2-post-search-button">'.esc_html__('Add Items','wp-food').'</span>',
		) );
	}
	function _edit_columns($columns){
		global $wpdb;
		$columns['_id'] = esc_html__( 'ID' , 'wp-food' );
		$columns['_order_status'] = esc_html__( 'Status' , 'wp-food' );		
		return $columns;
	}
	function _custom_columns_content( $column ) {
		global $post;
		switch ( $column ) {
			case '_id':
				$_id = $post->ID;
				echo '<span class="_id">'.wp_kses_post($_id).'</span>';
				break;
			case '_order_status':
				$_order_status = get_post_meta($post->ID, 'exfood_order_status', true);
				if($_order_status == 'cancel'){
					echo '<span class="_order_status-'.esc_attr($_order_status).'">'.esc_html__( 'Cancelled', 'wp-food' ).'</span>';
				}else if($_order_status == 'process'){
					echo '<span class="_order_status-'.esc_attr($_order_status).'">'.esc_html__( 'Processing', 'wp-food' ).'</span>';
				}else if($_order_status == 'complete'){
					 echo '<span class="_order_status-'.esc_attr($_order_status).'">'.esc_html__( 'Completed', 'wp-food' ).'</span>';
				}else{
					echo '<span class="_order_status-'.esc_attr($_order_status).'">'.esc_html__( 'On Hold', 'wp-food' ).'</span>';
				}
				break;	
		}
	}
}
$EXfood_Ordering_Posttype = new EXfood_Ordering_Posttype();