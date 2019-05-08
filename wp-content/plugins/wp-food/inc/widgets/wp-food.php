<?php
class exfood_Widget_wp_food extends WP_Widget {	
	function __construct() {
    	$widget_ops = array(
			'classname'   => 'ex-wp-food-widget', 
			'description' => esc_html__('Display your food from shortcode builder via widget','wp-food')
		);
    	parent::__construct('ex-widget', esc_html__('WP Food','wp-food'), $widget_ops);
	}
	function widget($args, $instance) {
		ob_start();
		extract($args);
		$title 			= empty($instance['title']) ? '' : $instance['title'];
		$title          = apply_filters('widget_title', $title);
		$id_sc 			= empty($instance['id_sc']) ? '' : $instance['id_sc'];
		$html = $before_widget;
		if ( $title ) $html .= $before_title . $title . $after_title;
		$html .= do_shortcode('[extpsc id="'.$id_sc.'"]');
		$html .= $after_widget;
		echo ' '.$html;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
        $instance['id_sc'] = strip_tags($new_instance['id_sc']);
		return $instance;
	}
	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$id_sc = isset($instance['id_sc']) ? esc_attr($instance['id_sc']) : '';
		?>
        <p><label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:','wp-food'); ?></label>
        <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id("sort_by")); ?>">
            <?php esc_html_e('Select Shortcode','wp-food');	 ?>:
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id("id_sc")); ?>" name="<?php echo esc_attr($this->get_field_name("id_sc")); ?>">
            	<?php 
            	echo '<option value="0" '.selected( $id_sc, "date",0 ).'>'. esc_html__('Choose a shortcode','wp-food').'</option>';
				$id_query = new WP_Query( 'post_type=exfood_scbd&posts_per_page=-1' );
				if ( $id_query->have_posts() ) {
					while ( $id_query->have_posts() ) {
						$id_query->the_post();
						$id_array[get_the_ID()] = get_the_title();
						echo '<option value="'.get_the_ID().'" '.selected( $id_sc, get_the_ID(),0 ).'>'. get_the_title().'</option>';
					}
				}
				wp_reset_postdata();
            	?>
            </select>
            </label>
        </p>
<?php
	}
}
// register widget
if(!function_exists('exfood_register_widgets')){
	function exfood_register_widgets() {
		register_widget( 'exfood_Widget_wp_food' );
	}
	add_action( 'widgets_init', 'exfood_register_widgets' );
}

