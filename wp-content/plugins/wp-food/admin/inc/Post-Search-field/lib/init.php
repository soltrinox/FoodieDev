<?php
/**
 * CMB2 Post Search field
 *
 * Custom field for CMB2 which adds a post-search dialog for
 * searching/attaching other post IDs
 *
 * @category WordPressLibrary
 * @package  CMB2_Post_Search_field
 * @author   WebDevstudios <contact@webdevstudios.com>
 * @license  GPL-2.0+
 * @version  0.2.5
 * @link     https://github.com/WebDevStudios/CMB2-Post-Search-field
 * @since    0.2.4
 */
class CMB2_Post_Search_field {

	protected static $single_instance = null;

	/**
	 * Creates or returns an instance of this class.
	 * @since  0.2.4
	 * @return CMB2_Post_Search_field A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	protected function __construct() {
		add_action( 'cmb2_render_post_search_text', array( $this, 'render_field' ), 10, 5 );
		add_action( 'cmb2_after_form', array( $this, 'render_js' ), 10, 4 );
		add_action( 'cmb2_post_search_field_add_find_posts_div', array( $this, 'add_find_posts_div' ) );
		add_action( 'admin_init', array( $this, 'ajax_find_posts' ) );

	}

	public function render_field( $field, $escaped_value, $object_id, $object_type, $field_type ) {
		echo ' '.$field_type->input( array(
			'data-search' => json_encode( array(
				'posttype'   => $field->args( 'post_type' ),
				'selecttype' => 'radio' == $field->args( 'select_type' ) ? 'radio' : 'checkbox',
				'selectbehavior' => 'replace' == $field->args( 'select_behavior' ) ? 'replace' : 'add',
				'errortxt'   => esc_attr( $field_type->_text( 'error_text', __( 'An error has occurred. Please reload the page and try again.' ) ) ),
				'findtxt'    => esc_attr( $field_type->_text( 'find_text', __( 'Find Posts or Pages' ) ) ),
			) ),
		) );
	}

	public function render_js(  $cmb_id, $object_id, $object_type, $cmb ) {
		static $rendered;

		if ( $rendered ) {
			return;
		}

		$fields = $cmb->prop( 'fields' );

		if ( ! is_array( $fields ) ) {
			return;
		}

		$has_post_search_field = $this->has_post_search_text_field( $fields );

		if ( ! $has_post_search_field ) {
			return;
		}

		// JS needed for modal
		// wp_enqueue_media();
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'wp-backbone' );

		if ( ! is_admin() ) {
			// Will need custom styling!
			// @todo add styles for front-end
			require_once( ABSPATH . 'wp-admin/includes/template.php' );
			do_action( 'cmb2_post_search_field_add_find_posts_div' );
		}

		// markup needed for modal
		add_action( 'admin_footer', 'find_posts_div' );

		// @TODO this should really be in its own JS file.
		?>
		<?php

		$rendered = true;
	}

	public function has_post_search_text_field( $fields ) {

		foreach ( $fields as $field ) {
			if ( isset( $field['fields'] ) ) {
				if ( $this->has_post_search_text_field( $field['fields'] ) ) {
					return true;
				}
			}
			if ( 'post_search_text' == $field['type'] ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Add the find posts div via a hook so we can relocate it manually
	 */
	public function add_find_posts_div() {
		add_action( 'wp_footer', 'find_posts_div' );
	}


	/**
	 * Check to see if we have a post type set and, if so, add the
	 * pre_get_posts action to set the queried post type
	 */
	public function ajax_find_posts() {
		if (
			defined( 'DOING_AJAX' )
			&& DOING_AJAX
			&& isset( $_POST['cmb2_post_search'], $_POST['action'], $_POST['post_search_cpt'] )
			&& 'find_posts' == $_POST['action']
			&& ! empty( $_POST['post_search_cpt'] )
		) {
			add_action( 'pre_get_posts', array( $this, 'set_post_type' ) );
		}
	}

	/**
	 * Set the post type via pre_get_posts
	 * @param  array $query  The query instance
	 */
	public function set_post_type( $query ) {
		$types = $_POST['post_search_cpt'];
		$types = is_array( $types ) ? array_map( 'esc_attr', $types ) : esc_attr( $types );
		$query->set( 'post_type', $types );
	}

}
CMB2_Post_Search_field::get_instance();

// preserve a couple functions for back-compat.


if ( ! function_exists( 'cmb2_post_search_render_field' ) ) {
	function cmb2_post_search_render_field( $field, $escaped_value, $object_id, $object_type, $field_type ) {
		_deprecated_function( __FUNCTION__, '0.2.4', 'Please access these methods through the CMB2_Post_Search_field::get_instance() object.' );

		return CMB2_Post_Search_field::get_instance()->render_field( $field, $escaped_value, $object_id, $object_type, $field_type );
	}
}

// Remove old versions.
remove_action( 'cmb2_render_post_search_text', 'cmb2_post_search_render_field', 10, 5 );
remove_action( 'cmb2_after_form', 'cmb2_post_search_render_js', 10, 4 );

if ( ! function_exists( 'cmb2_has_post_search_text_field' ) ) {
	function cmb2_has_post_search_text_field( $fields ) {
		_deprecated_function( __FUNCTION__, '0.2.4', 'Please access these methods through the CMB2_Post_Search_field::get_instance() object.' );

		return CMB2_Post_Search_field::get_instance()->has_post_search_text_field( $fields );
	}
}
