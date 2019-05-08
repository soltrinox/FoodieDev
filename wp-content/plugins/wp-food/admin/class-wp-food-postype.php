<?php
include 'inc/metadata-functions.php';
class EX_WPFood_Posttype {
	public function __construct()
    {
        add_action( 'init', array( &$this, 'register_post_type' ) );
		add_action( 'init', array( &$this, 'register_category_taxonomies' ) );
		add_action( 'init', array( &$this, 'register_location_taxonomies' ) );
    }

	function register_post_type(){
		$labels = array(
			'name'               => esc_html__('Food','wp-food'),
			'singular_name'      => esc_html__('Food','wp-food'),
			'add_new'            => esc_html__('Add New Food','wp-food'),
			'add_new_item'       => esc_html__('Add New Food','wp-food'),
			'edit_item'          => esc_html__('Edit Food','wp-food'),
			'new_item'           => esc_html__('New Food','wp-food'),
			'all_items'          => esc_html__('Food','wp-food'),
			'view_item'          => esc_html__('View Food','wp-food'),
			'search_items'       => esc_html__('Search Food','wp-food'),
			'not_found'          => esc_html__('No Food found','wp-food'),
			'not_found_in_trash' => esc_html__('No Food found in Trash','wp-food'),
			'parent_item_colon'  => '',
			'menu_name'          => esc_html__('Food','wp-food')
		);
		
		$exfood_single_slug = exfood_get_option('exfood_single_slug');
		if($exfood_single_slug==''){
			$exfood_single_slug = 'food';
		}
		$rewrite =  array( 'slug' => untrailingslashit( $exfood_single_slug ), 'with_front' => false, 'feeds' => true );
		
		$args = array(  
			'labels' => $labels,  
			'menu_position' => 8, 
			'supports' => array('title','editor','thumbnail', 'excerpt','custom-fields'),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'menu_icon' =>  'dashicons-store',
			'query_var'          => true,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'rewrite' => $rewrite,
		);  
		register_post_type('ex_food',$args);  
	}
	function register_category_taxonomies(){
		$labels = array(
			'name'              => esc_html__( 'Menu', 'wp-food' ),
			'singular_name'     => esc_html__( 'Menu', 'wp-food' ),
			'search_items'      => esc_html__( 'Menu','wp-food' ),
			'all_items'         => esc_html__( 'All Menu','wp-food' ),
			'parent_item'       => esc_html__( 'Parent Menu' ,'wp-food'),
			'parent_item_colon' => esc_html__( 'Parent Menu:','wp-food' ),
			'edit_item'         => esc_html__( 'Edit Menu' ,'wp-food'),
			'update_item'       => esc_html__( 'Update Menu','wp-food' ),
			'add_new_item'      => esc_html__( 'Add New Menu' ,'wp-food'),
			'menu_name'         => esc_html__( 'Menus','wp-food' ),
		);			
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'food-menu' ),
		);
		register_taxonomy('exfood_cat', 'ex_food', $args);
	}
	function register_location_taxonomies(){
		$labels = array(
			'name'              => esc_html__( 'Location', 'wp-food' ),
			'singular_name'     => esc_html__( 'Location', 'wp-food' ),
			'search_items'      => esc_html__( 'Location','wp-food' ),
			'all_items'         => esc_html__( 'All Location','wp-food' ),
			'parent_item'       => esc_html__( 'Parent Location' ,'wp-food'),
			'parent_item_colon' => esc_html__( 'Parent Location:','wp-food' ),
			'edit_item'         => esc_html__( 'Edit Location' ,'wp-food'),
			'update_item'       => esc_html__( 'Update Location','wp-food' ),
			'add_new_item'      => esc_html__( 'Add New Location' ,'wp-food'),
			'menu_name'         => esc_html__( 'Locations','wp-food' ),
		);			
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'food-menu' ),
		);
		register_taxonomy('exfood_loc', array( 'exfood_store','ex_food' ), $args);
	}	
}
$EX_WPFood_Posttype = new EX_WPFood_Posttype();