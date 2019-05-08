<?php
/**
 * Include and setup custom metaboxes and fields. (make sure you copy this file to outside the CMB2 directory)
 *
 * Be sure to replace all instances of 'yourprefix_' with your project's prefix.
 * http://nacin.com/2010/05/11/in-wordpress-prefix-everything/
 *
 * @category YourThemeOrPlugin
 * @package  Demo_CMB2
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/CMB2/CMB2
 */

/**
 * Get the bootstrap! If using the plugin from wordpress.org, REMOVE THIS!
 */

if ( file_exists( dirname( __FILE__ ) . '/cmb2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/cmb2/init.php';
} elseif ( file_exists( dirname( __FILE__ ) . '/CMB2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/CMB2/init.php';
}

require_once dirname( __FILE__ ) . '/Post-Search-field/cmb2_post_search_field.php';

function exfood_get_option( $key = '', $tab=false, $default = false ) {
	if(isset($tab) && $tab!=''){
		$option_key = $tab;
	}else{
		$option_key = 'exfood_options';
	}
	if ( function_exists( 'cmb2_get_option' ) ) {
		// Use cmb2_get_option as it passes through some key filters.
		return cmb2_get_option( $option_key, $key, $default );
	}
	// Fallback to get_option if CMB2 is not loaded yet.
	$opts = get_option( $option_key, $default );
	$val = $default;
	if ( 'all' == $key ) {
		$val = $opts;
	} elseif ( is_array( $opts ) && array_key_exists( $key, $opts ) && false !== $opts[ $key ] ) {
		$val = $opts[ $key ];
	}
	return $val;
}

add_action( 'cmb2_admin_init', 'exfood_register_metabox' );
/**
 * Hook in and add a demo metabox. Can only happen on the 'cmb2_admin_init' or 'cmb2_init' hook.
 */
function exfood_convert_number_to_show($value, $field_args, $field ){
	$exfood_decimal_sep = exfood_get_option('exfood_decimal_sep');
	if($exfood_decimal_sep!='.'){
		$value = str_replace(".",$exfood_decimal_sep,$value);
	}
	return $value;
}
function exfood_convert_number_to_save($value, $field_args, $field ){
	$exfood_decimal_sep = exfood_get_option('exfood_decimal_sep');
	if($exfood_decimal_sep!='.'){
		$value = str_replace($exfood_decimal_sep,".",$value);
	}
	if(!is_numeric($value)){return;}
	return $value;
}
function exfood_verify_money_js($field_args, $field){
	$exfood_decimal_sep = exfood_get_option('exfood_decimal_sep');
	?>
	<div class="exfood-money-info <?php echo $field_args['_id'];?>" style="display: none;"><?php echo sprintf( __( 'Please enter in monetary decimal (%s) format without thousand separators and currency.', 'wp-food' ), $exfood_decimal_sep); ?></div>
	<script type="text/javascript">
		jQuery( document ).ready( function( $ ) {
			$("#<?php echo $field_args['_id'];?>").on("keyup", function() {
				$val = this.value;
			    var re = /[^\-0-9\%\\' + <?php echo $exfood_decimal_sep; ?> + ']+$/;
    			if(re.test($val)){
    				$(".<?php echo $field_args['_id'];?>.exfood-money-info").fadeIn();
    			}else{
    				$(".<?php echo $field_args['_id'];?>.exfood-money-info").fadeOut();
    			}
			});
		});
	</script>
	<?php
}
function exfood_register_metabox() {
	$prefix = 'exfood_';

	/**
	 * Food general info
	 */
	$team_info = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => esc_html__( 'Food info', 'wp-food' ),
		'object_types'  => array( 'ex_food' ), // Post type
	) );

	$team_info->add_field( array(
		'name'       => esc_html__( 'Price', 'wp-food' ),
		'desc'       => esc_html__( 'Enter price', 'wp-food' ),
		'id'         => $prefix . 'price',
		'type'       => 'text',
		'classes'		 => 'column-2',
		'sanitization_cb' => 'exfood_convert_number_to_save',
		'escape_cb'       => 'exfood_convert_number_to_show',
		'after_field'  => 'exfood_verify_money_js',
	) );

	$team_info->add_field( array(
		'name'       => esc_html__( 'Sale Price', 'wp-food' ),
		'desc'       => esc_html__( 'Enter Sale price', 'wp-food' ),
		'id'         => $prefix . 'sale_price',
		'type'       => 'text',
		'classes'		 => 'column-2',
		'sanitization_cb' => 'exfood_convert_number_to_save',
		'escape_cb'       => 'exfood_convert_number_to_show',
		'after_field'  => 'exfood_verify_money_js',
	) );

	$team_info->add_field( array(
		'name'       => esc_html__( 'Protein', 'wp-food' ),
		'desc'       => esc_html__( 'Example: 50mg', 'wp-food' ),
		'id'         => $prefix . 'protein',
		'type'       => 'text',
		'classes'		 => 'column-3',
	) );
	$team_info->add_field( array(
		'name'       => esc_html__( 'Calories', 'wp-food' ),
		'desc'       => esc_html__( 'Example: 50mg', 'wp-food' ),
		'id'         => $prefix . 'calo',
		'type'       => 'text',
		'classes'		 => 'column-3',
	) );
	$team_info->add_field( array(
		'name'       => esc_html__( 'Cholesterol', 'wp-food' ),
		'desc'       => esc_html__( 'Example: 50mg', 'wp-food' ),
		'id'         => $prefix . 'choles',
		'type'       => 'text',
		'classes'		 => 'column-3',
	) );
	$team_info->add_field( array(
		'name'       => esc_html__( 'Dietary fibre', 'wp-food' ),
		'desc'       => esc_html__( 'Example: 50mg', 'wp-food' ),
		'id'         => $prefix . 'fibel',
		'type'       => 'text',
		'classes'		 => 'column-4',
	) );
	$team_info->add_field( array(
		'name'       => esc_html__( 'Sodium', 'wp-food' ),
		'desc'       => esc_html__( 'Example: 50mg', 'wp-food' ),
		'id'         => $prefix . 'sodium',
		'type'       => 'text',
		'classes'		 => 'column-4',
	) );
	$team_info->add_field( array(
		'name'       => esc_html__( 'Carbohydrates', 'wp-food' ),
		'desc'       => esc_html__( 'Example: 50mg', 'wp-food' ),
		'id'         => $prefix . 'carbo',
		'type'       => 'text',
		'classes'		 => 'column-4',
	) );
	$team_info->add_field( array(
		'name'       => esc_html__( 'Fat total', 'wp-food' ),
		'desc'       => esc_html__( 'Example: 50mg', 'wp-food' ),
		'id'         => $prefix . 'fat',
		'type'       => 'text',
		'classes'		 => 'column-4',
	) );
	$team_info->add_field( array(
		'name' => esc_html__( 'Image gallery', 'wp-food' ),
		'desc' => '',
		'id'   => $prefix . 'gallery',
		'type' => 'file_list',
		'query_args' => array( 'type' => 'image' ), // Only images attachment
	) );
	/**
	 * Build-in ordering system
	 */
	if(exfood_get_option('exfood_booking') !='woo'){
		$addition_option = new_cmb2_box( array(
			'id'            => $prefix . 'addition_options',
			'title'         => esc_html__( 'Additional option', 'wp-food' ),
			'object_types'  => array( 'ex_food' ), // Post type
		) );
		$group_option = $addition_option->add_field( array(
			'id'          => $prefix . 'addition_data',
			'type'        => 'group',
			'description' => esc_html__( 'Add additional food option to allow user can order with this food', 'wp-food' ),
			// 'repeatable'  => false, // use false if you want non-repeatable group
			'options'     => array(
				'group_title'   => esc_html__( 'Option {#}', 'wp-food' ), // since version 1.1.4, {#} gets replaced by row number
				'add_button'    => esc_html__( 'Add Option', 'wp-food' ),
				'remove_button' => esc_html__( 'Remove Option', 'wp-food' ),
				'sortable'      => true, // beta
				// 'closed'     => true, // true to have the groups closed by default
			),
			'after_group' => 'exfood_repeatable_titles_for_options',
		) );
		// Id's for group's fields only need to be unique for the group. Prefix is not needed.
		$addition_option->add_group_field( $group_option, array(
			'name' => esc_html__( 'Name', 'wp-food' ),
			'id'   => '_name',
			'type' => 'text',
			// 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
		) );
		$addition_option->add_group_field( $group_option, array(
			'name' => esc_html__( 'Option type', 'wp-food' ),
			'description' => esc_html__( 'Select type of this option', 'wp-food' ),
			'id'   => '_type',
			'type' => 'select',
			'show_option_none' => false,
			'default' => '',
			'options'          => array(
				'' => esc_html__( 'Checkboxes', 'wp-food' ),
				'radio'   => esc_html__( 'Radio buttons', 'wp-food' ),
				'select'   => esc_html__( 'Select box', 'wp-food' ),
			),
		) );
		$addition_option->add_group_field( $group_option, array(
			'name' => esc_html__( 'Required?', 'wp-food' ),
			'description' => esc_html__( 'Select this option is required or not', 'wp-food' ),
			'id'   => '_required',
			'type' => 'select',
			'show_option_none' => false,
			'default' => '',
			'options'          => array(
				'' => esc_html__( 'No', 'wp-food' ),
				'radio'   => esc_html__( 'Yes', 'wp-food' ),
			),
		) );
		$addition_option->add_group_field( $group_option, array(
			'name' => esc_html__( 'Options', 'wp-food' ),
			'description' => esc_html__( 'Enter name of option and price separator by | Example: Option 1 | 100', 'wp-food' ),
			'id'   => '_value',
			'type' => 'text',
			'repeatable'     => true,
			'attributes'  => array(
				'placeholder' => esc_html__( 'Name | Price', 'wp-food' ),
			),
		) );
	}else{
	/**
	 * WooCommerce ordering
	 */

		$woo_info = new_cmb2_box( array(
			'id'            => $prefix . 'woocommerce',
			'title'         => esc_html__( 'Food ordering via WooCommerce', 'wp-food' ),
			'object_types'  => array( 'ex_food' ), // Post type
		) );
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if (is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$html_link = $wprice = $wsprice = $wsku ='';
			if(isset($_GET['post']) && is_numeric($_GET['post'])){
				$product_exist = get_post_meta( $_GET['post'], $prefix . 'product', true );
				if($product_exist !='' && is_numeric($product_exist)){
					$edit_link = get_edit_post_link( $product_exist, true );
					$html_link = '<a href="'.esc_url($edit_link).'">'.esc_html__('Edit product in WooCommerce','wp-food').'</a>';
					$_product = wc_get_product( $product_exist );
					if($_product!=''){
						$wsku = $_product->get_sku();
						$type = $_product->get_type();
						if($type=='variable'){
						}else{
							$wsprice = $_product->get_sale_price();
							$wprice = $_product->get_price();
						}
					}
				}
			}
			$woo_info->add_field( array(
				'name'        => esc_html__( 'Product' ),
				'id'          => $prefix . 'product',
				'type'        => 'post_search_text', 
				'desc'       => esc_html__( 'Select or enter id of existing product or add new bellow', 'wp-food' ),
				'post_type'   => 'product',
				'select_type' => 'radio',
				'select_behavior' => 'replace',
				'after_field'  => $html_link,
			) );
			$woo_info->add_field( array(
				'name'       => esc_html__( 'Price', 'wp-food' ),
				'desc'       => esc_html__( 'Enter price', 'wp-food' ),
				'id'         => $prefix . 'wooprice',
				'type'       => 'title',
				'classes'		 => 'column-3',
				'after_field'  => '<input type="text" name="woo_price" value="'.esc_attr($wprice).'"/>',
			) );
			$woo_info->add_field( array(
				'name'       => esc_html__( 'Sale Price', 'wp-food' ),
				'desc'       => esc_html__( 'Enter Sale price', 'wp-food' ),
				'id'         => $prefix . 'woosprice',
				'type'       => 'title',
				'classes'		 => 'column-3',
				'after_field'  => '<input type="text" name="woo_sprice" value="'.esc_attr($wsprice).'"/>',
			) );
			$woo_info->add_field( array(
				'name'       => esc_html__( 'Sku', 'wp-food' ),
				'desc'       => esc_html__( 'Enter Sku price', 'wp-food' ),
				'id'         => $prefix . 'woosku',
				'type'       => 'title',
				'classes'		 => 'column-3',
				'after_field'  => '<input type="text" name="woo_sku" value="'.esc_attr($wsku).'"/>',
			) );
		}else{
			$woo_info->add_field( array(
				'name'       => esc_html__( 'WooCommerce is Required to use this feature, please install or activate WooCommerce plugin', 'wp-food' ),
				'desc'       => '',
				'id'         => $prefix . 'info',
				'type'       => 'title',
				'classes'		 => '',
			) );
		}
	}

	$custom_data = new_cmb2_box( array(
		'id'            => $prefix . 'custom_data',
		'title'         => esc_html__( 'Food Custom Info', 'wp-food' ),
		'object_types'  => array( 'ex_food' ),
	) );
	$group_data = $custom_data->add_field( array(
		'id'          => $prefix . 'custom_data_gr',
		'type'        => 'group',
		'description' => esc_html__( 'Add food info, example: Fat saturated... Or anything you want to show', 'wp-food' ),
		// 'repeatable'  => false, // use false if you want non-repeatable group
		'options'     => array(
			'group_title'   => esc_html__( 'Food Info {#}', 'wp-food' ), // since version 1.1.4, {#} gets replaced by row number
			'add_button'    => esc_html__( 'Add Another Food info', 'wp-food' ),
			'remove_button' => esc_html__( 'Remove Custom Food info', 'wp-food' ),
			'sortable'      => true, // beta
			// 'closed'     => true, // true to have the groups closed by default
		),
		'after_group' => 'exfood_add_js_for_repeatable_titles',
	) );
	// Id's for group's fields only need to be unique for the group. Prefix is not needed.
	$custom_data->add_group_field( $group_data, array(
		'name' => esc_html__( 'Name', 'wp-food' ),
		'id'   => '_name',
		'type' => 'text',
		// 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
	) );
	$custom_data->add_group_field( $group_data, array(
		'name' => esc_html__( 'Info', 'wp-food' ),
		'description' => '',
		'id'   => '_value',
		'type' => 'text',
	) );
}
// Regiter metadata fo menu
add_action( 'cmb2_admin_init', 'exfood_register_taxonomy_metabox' );
function exfood_register_taxonomy_metabox() {
	$prefix = 'exfood_menu_';
	/**
	 * Metabox to add fields to categories and tags
	 */
	$cmb_term = new_cmb2_box( array(
		'id'               => $prefix . 'data',
		'title'            => esc_html__( 'Category Metabox', 'wp-food' ), // Doesn't output for term boxes
		'object_types'     => array( 'term' ), // Tells CMB2 to use term_meta vs post_meta
		'taxonomies'       => array( 'exfood_cat'), // Tells CMB2 which taxonomies should have these fields
		'new_term_section' => true, // Will display in the "Add New Category" section
	) );
	/*$cmb_term->add_field( array(
		'name' => esc_html__( 'Menu Image', 'wp-food' ),
		'desc' => esc_html__( 'Set image url for menu', 'wp-food' ),
		'id'   => $prefix . 'img',
		'type' => 'file',
		'options' => array(
			'url' => false, // Hide the text input for the url
		),
		'query_args' => array(
			'type' => array(
				'image/gif',
				'image/jpeg',
				'image/png',
			),
		),
		'text'    => array(
			'add_upload_file_text' => esc_html__( 'Select Image', 'wp-food' ),
		),
	) );*/
	$cmb_term->add_field( array(
		'name' => esc_html__( 'Menu Icon', 'wp-food' ),
		'desc' => esc_html__( 'Set icon image for menu', 'wp-food' ),
		'id'   => $prefix . 'icon',
		'type' => 'file',
		'options' => array(
			'url' => false, // Hide the text input for the url
		),
		'query_args' => array(
			'type' => array(
				'image/gif',
				'image/jpeg',
				'image/png',
				'image/svg',
			),
		),
		'preview_size' => 'medium',
		'text'    => array(
			'add_upload_file_text' => esc_html__( 'Select Image', 'wp-food' ),
		),
	) );
}




function exfood_allow_metadata_save_html( $original_value, $args, $cmb2_field ) {
    return $original_value; // Unsanitized value.
}
function exfood_add_js_for_repeatable_titles() {
	add_action( is_admin() ? 'admin_footer' : 'wp_footer', 'exfood_js_repeatable_titles_custom_data' );
}
function exfood_js_repeatable_titles_custom_data() {
	exfood_js_for_repeatable_titles('exfood_custom_data');
}
function exfood_repeatable_titles_for_options() {
	add_action( is_admin() ? 'admin_footer' : 'wp_footer', 'exfood_js_repeatable_titles_options' );
}
function exfood_js_repeatable_titles_options() {
	exfood_js_for_repeatable_titles('exfood_addition_options');
}
function exfood_js_for_repeatable_titles($id) {
	
}
/**
 * Callback to define the optionss-saved message.
 *
 * @param CMB2  $cmb The CMB2 object.
 * @param array $args {
 *     An array of message arguments
 *
 *     @type bool   $is_options_page Whether current page is this options page.
 *     @type bool   $should_notify   Whether options were saved and we should be notified.
 *     @type bool   $is_updated      Whether options were updated with save (or stayed the same).
 *     @type string $setting         For add_settings_error(), Slug title of the setting to which
 *                                   this error applies.
 *     @type string $code            For add_settings_error(), Slug-name to identify the error.
 *                                   Used as part of 'id' attribute in HTML output.
 *     @type string $message         For add_settings_error(), The formatted message text to display
 *                                   to the user (will be shown inside styled `<div>` and `<p>` tags).
 *                                   Will be 'Settings updated.' if $is_updated is true, else 'Nothing to update.'
 *     @type string $type            For add_settings_error(), Message type, controls HTML class.
 *                                   Accepts 'error', 'updated', '', 'notice-warning', etc.
 *                                   Will be 'updated' if $is_updated is true, else 'notice-warning'.
 * }
 */
function exfood_options_page_message_( $cmb, $args ) {
	if ( ! empty( $args['should_notify'] ) ) {

		if ( $args['is_updated'] ) {

			// Modify the updated message.
			$args['message'] = sprintf( esc_html__( '%s &mdash; Updated!', 'wp-food' ), $cmb->prop( 'title' ) );
		}

		add_settings_error( $args['setting'], $args['code'], $args['message'], $args['type'] );
	}
}


function exfood_register_setting_options() {
	/**
	 * Registers main options page menu item and form.
	 */
	$args = array(
		'id'           => 'exfood_options_page',
		'title'        => esc_html__('Settings','wp-food'),
		'object_types' => array( 'options-page' ),
		'option_key'   => 'exfood_options',
		'parent_slug'  => 'edit.php?post_type=ex_food',
		'tab_group'    => 'exfood_options',
		'tab_title'    => esc_html__('General','wp-food'),
		'message_cb'      => 'exfood_options_page_message_',
	);
	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'exfood_options_display_with_tabs';
	}
	$main_options = new_cmb2_box( $args );
	/**
	 * Options fields ids only need
	 * to be unique within this box.
	 * Prefix is not needed.
	 */
	$main_options->add_field( array(
		'name'    => esc_html__('Main Color','wp-food'),
		'desc'    => esc_html__('Choose Main Color for plugin','wp-food'),
		'id'      => 'exfood_color',
		'type'    => 'colorpicker',
		'default' => '',
	) );
	$main_options->add_field( array(
		'name'       => esc_html__( 'Content Font Family', 'wp-food' ),
		'desc'       => esc_html__('Enter Google font-family name . For example, if you choose "Source Sans Pro" Google Font, enter Source Sans Pro','wp-food'),
		'id'         => 'exfood_font_family',
		'type'       => 'text',
		'default' => '',
	) );
	$main_options->add_field( array(
		'name'       => esc_html__( 'Content Font Size', 'wp-food' ),
		'desc'       => esc_html__('Enter size of main font, default:13px, Ex: 14px','wp-food'),
		'id'         => 'exfood_font_size',
		'type'       => 'text',
		'default' => '',
	) );
	$main_options->add_field( array(
		'name'    => esc_html__('Content Font Color','wp-food'),
		'desc'    => esc_html__('Choose Content Font Color for plugin','wp-food'),
		'id'      => 'exfood_ctcolor',
		'type'    => 'colorpicker',
		'default' => '',
	) );
	$main_options->add_field( array(
		'name'       => esc_html__( 'Heading Font Family', 'wp-food' ),
		'desc'       => esc_html__('Enter Google font-family name. For example, if you choose "Oswald" Google Font, enter Oswald','wp-food'),
		'id'         => 'exfood_headingfont_family',
		'type'       => 'text',
		'default' => '',
	) );
	$main_options->add_field( array(
		'name'       => esc_html__( 'Heading Font Size', 'wp-food' ),
		'desc'       => esc_html__('Enter size of heading font, default: 20px, Ex: 22px','wp-food'),
		'id'         => 'exfood_headingfont_size',
		'type'       => 'text',
		'default' => '',
	) );
	$main_options->add_field( array(
		'name'    => esc_html__('Heading Font Color','wp-food'),
		'desc'    => esc_html__('Choose Heading Font Color for plugin','wp-food'),
		'id'      => 'exfood_hdcolor',
		'type'    => 'colorpicker',
		'default' => '',
	) );
	$main_options->add_field( array(
		'name'       => esc_html__( 'Price Font Family', 'wp-food' ),
		'desc'       => esc_html__('Enter Google font-family name. For example, if you choose "Oswald" Google Font, enter Oswald','wp-food'),
		'id'         => 'exfood_pricefont_family',
		'type'       => 'text',
		'default' => '',
	) );
	$main_options->add_field( array(
		'name'       => esc_html__( 'Price Font Size', 'wp-food' ),
		'desc'       => esc_html__('Enter size of Price font, default: 20px, Ex: 22px','wp-food'),
		'id'         => 'exfood_pricefont_size',
		'type'       => 'text',
		'default' => '',
	) );
	$main_options->add_field( array(
		'name'    => esc_html__('Price Font Color','wp-food'),
		'desc'    => esc_html__('Choose Price Font Color for plugin','wp-food'),
		'id'      => 'exfood_pricecolor',
		'type'    => 'colorpicker',
		'default' => '',
	) );
	$main_options->add_field( array(
		'name'       => esc_html__( 'Meta Font Family', 'wp-food' ),
		'desc'       => esc_html__('Enter Google font-family name. For example, if you choose "Ubuntu" Google Font, enter Ubuntu','wp-food'),
		'id'         => 'exfood_metafont_family',
		'type'       => 'text',
		'default' => '',
	) );
	$main_options->add_field( array(
		'name'       => esc_html__( 'Meta Font Size', 'wp-food' ),
		'desc'       => esc_html__('Enter size of metadata font, default:13px, Ex: 12px','wp-food'),
		'id'         => 'exfood_metafont_size',
		'type'       => 'text',
		'default' => '',
	) );
	$main_options->add_field( array(
		'name'    => esc_html__('Meta Font Color','wp-food'),
		'desc'    => esc_html__('Choose Meta Font Color for plugin','wp-food'),
		'id'      => 'exfood_mtcolor',
		'type'    => 'colorpicker',
		'default' => '',
	) );
	
	$main_options->add_field( array(
		'name'             => esc_html__( 'Disable link & Single food page', 'wp-food' ),
		'desc'             => esc_html__( 'Select yes to disable link to single member page', 'wp-food' ),
		'id'               => 'exfood_disable_single',
		'type'             => 'select',
		'show_option_none' => false,
		'default' => '',
		'options'          => array(
			'' => esc_html__( 'No', 'wp-food' ),
			'yes'   => esc_html__( 'Yes', 'wp-food' ),
		),
	) );
	$main_options->add_field( array(
		'name'             => esc_html__( 'RTL mode', 'wp-food' ),
		'desc'             => esc_html__( 'Enable RTL mode for RTL language', 'wp-food' ),
		'id'               => 'exfood_enable_rtl',
		'type'             => 'select',
		'show_option_none' => false,
		'default' => '',
		'options'          => array(
			'' => esc_html__( 'No', 'wp-food' ),
			'yes'   => esc_html__( 'Yes', 'wp-food' ),
		),
	) );
	$main_options->add_field( array(
		'name'             => esc_html__( 'Food slug', 'wp-food' ),
		'desc'             => esc_html__( 'Remember to save the permalink settings again in Settings > Permalinks', 'wp-food' ),
		'show_on_cb' => 'exfood_hide_if_disable_single',
		'id'               => 'exfood_single_slug',
		'type'       => 'text',
		'default' => '',
	) );
	$main_options->add_field( array(
		'name'             => esc_html__( 'Enable popup location', 'wp-food' ),
		'desc'             => esc_html__( 'Select yes to enable popup select location', 'wp-food' ),
		'id'               => 'exfood_enable_loc',
		'type'             => 'select',
		'default' 		   => '',
		'show_option_none' => false,
		'options'          => array(
			'' => esc_html__( 'No', 'wp-food' ),
			'yes'   => esc_html__( 'Yes', 'wp-food' ),
		),
	) );
	$main_options->add_field( array(
		'name'             => esc_html__( 'Popup location icon', 'wp-food' ),
		'desc'             => esc_html__( 'Select Icon for location popup, only work when enable popup location', 'wp-food' ),
		'id'               => 'exfood_loc_icon',
		'type'             => 'file',
		'default' 		   => '',
		'show_option_none' => false,
		'query_args' => array(
			'type' => array(
				'image/gif',
				'image/jpeg',
				'image/png',
			),
		),
		'preview_size' => array( 50, 50 ),
	) );
	$main_options->add_field( array(
		'name'       => esc_html__( 'Currency', 'wp-food' ),
		'desc'       => esc_html__( 'Enter Currency(Default: $)', 'wp-food' ),
		'id'         => 'exfood_currency',
		'default' => '',
		'type'       => 'text',
	) );
	$main_options->add_field( array(
		'name'       => esc_html__( 'Currency Position', 'wp-food' ),
		'desc'       => esc_html__( 'Select Currency Position', 'wp-food' ),
		'id'         => 'exfood_position',
		'type'             => 'select',
		'show_option_none' => false,
		'default'          => '0',
		'options'          => array(
			'0'   => __( 'After Price', 'wp-food' ),
			'1' => __( 'Before Price', 'wp-food' ),	
		),
	) );
	$main_options->add_field( array(
		'name'       => esc_html__( 'Thousand separator', 'wp-food' ),
		'desc'       => esc_html__( 'Input Thousand separator', 'wp-food' ),
		'id'         => 'exfood_thousand_sep',
		'default' => ',',
		'type'       => 'text',
	) );
	$main_options->add_field( array(
		'name'       => esc_html__( 'Decimal separator', 'wp-food' ),
		'desc'       => esc_html__( 'Input Decimal separator', 'wp-food' ),
		'id'         => 'exfood_decimal_sep',
		'default' => '.',
		'type'       => 'text',
	) );
	$main_options->add_field( array(
		'name'       => esc_html__( 'Number of decimals', 'wp-food' ),
		'desc'       => esc_html__( 'Input Number of decimals', 'wp-food' ),
		'id'         => 'exfood_num_decimal',
		'type'       => 'text',
		'default' => '0',
		'attributes' => array(
			'type' => 'number',
			'min' => '0',
			'max' => '20',
			'step' => '1',
		),
	) );
	$main_options->add_field( array(
		'name'       => esc_html__( 'Food ordering via', 'wp-food' ),
		'desc'       => esc_html__( 'Select booking system when user order food', 'wp-food' ),
		'id'         => 'exfood_booking',
		'type'             => 'select',
		'show_option_none' => false,
		'default'          => '0',
		'options'          => array(
			'cf7'   => __( 'Build-in', 'wp-food' ),
			'woo' => __( 'WooCommerce', 'wp-food' ),	
		),
	) );
	/**
	 * Registers Advanced options page, and set main item as parent.
	 */
	$args = array(
		'id'           => 'exfood_advanced',
		'menu_title'   => '',
		'object_types' => array( 'options-page' ),
		'option_key'   => 'exfood_advanced_options',
		'parent_slug'  => 'edit.php?post_type=ex_food',
		'tab_group'    => 'exfood_options',
		'tab_title'    => esc_html__('Advanced','wp-food'),
	);
	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'exfood_options_display_with_tabs';
	}
	$adv_options = new_cmb2_box( $args );
	$adv_options->add_field( array(
		'name' => esc_html__('Cart page','wp-food'),
		'desc' => esc_html__('Select Page with content:[exfood_cart]','wp-food'),
		'id'   => 'exfood_cart_page',
		'type'        => 'post_search_text', 
		'post_type'   => 'page',
		'select_type' => 'radio',
		'select_behavior' => 'replace',
	) );
	$adv_options->add_field( array(
		'name' => esc_html__('Checkout','wp-food'),
		'desc' => esc_html__('Select Page with content:[exfood_checkout]','wp-food'),
		'id'   => 'exfood_checkout_page',
		'type'        => 'post_search_text', 
		'post_type'   => 'page',
		'select_type' => 'radio',
		'select_behavior' => 'replace',
	) );
	$adv_options->add_field( array(
		'name' => esc_html__('Email recipients','wp-food'),
		'desc' => esc_html__('Enter recipients (comma separated) for this email. Defaults to admin email.','wp-food'),
		'id'   => 'exfood_email_Recipient',
		'type'        => 'text', 
	) );
	$adv_options->add_field( array(
		'name' => esc_html__('Checkout field','wp-food'),
		'desc' => '',
		'id'   => 'exfood_checkout_field',
		'type'        => 'title', 
	) );
	$adv_options->add_field( array(
		'name'       => esc_html__( 'Billing First name required', 'wp-food' ),
		'desc'       => esc_html__( 'Select yes to make this field is required', 'wp-food' ),
		'id'         => 'exfood_ck_fname',
		'type'             => 'select',
		'show_option_none' => false,
		'default'          => '',
		'options'          => array(
			''   => __( 'Yes', 'wp-food' ),
			'no' => __( 'No', 'wp-food' ),	
		),
	) );
	$adv_options->add_field( array(
		'name'       => esc_html__( 'Billing Last name required', 'wp-food' ),
		'desc'       => esc_html__( 'Select yes to make this field is required', 'wp-food' ),
		'id'         => 'exfood_ck_lname',
		'type'             => 'select',
		'show_option_none' => false,
		'default'          => '',
		'options'          => array(
			''   => __( 'Yes', 'wp-food' ),
			'no' => __( 'No', 'wp-food' ),	
		),
	) );
	$adv_options->add_field( array(
		'name'       => esc_html__( 'Billing Location required', 'wp-food' ),
		'desc'       => esc_html__( 'Select yes to make this field is required', 'wp-food' ),
		'id'         => 'exfood_ck_location',
		'type'             => 'select',
		'show_option_none' => false,
		'default'          => '',
		'options'          => array(
			''   => __( 'Yes', 'wp-food' ),
			'no' => __( 'No', 'wp-food' ),	
		),
	) );
	$adv_options->add_field( array(
		'name'       => esc_html__( 'Billing Address required', 'wp-food' ),
		'desc'       => esc_html__( 'Select yes to make this field is required', 'wp-food' ),
		'id'         => 'exfood_ck_address',
		'type'             => 'select',
		'show_option_none' => false,
		'default'          => '',
		'options'          => array(
			''   => __( 'Yes', 'wp-food' ),
			'no' => __( 'No', 'wp-food' ),	
		),
	) );
	$adv_options->add_field( array(
		'name'       => esc_html__( 'Billing Phone required', 'wp-food' ),
		'desc'       => esc_html__( 'Select yes to make this field is required', 'wp-food' ),
		'id'         => 'exfood_ck_phone',
		'type'             => 'select',
		'show_option_none' => false,
		'default'          => '',
		'options'          => array(
			''   => __( 'Yes', 'wp-food' ),
			'no' => __( 'No', 'wp-food' ),	
		),
	) );
	$adv_options->add_field( array(
		'name'       => esc_html__( 'Billing Email required', 'wp-food' ),
		'desc'       => esc_html__( 'Select yes to make this field is required', 'wp-food' ),
		'id'         => 'exfood_ck_email',
		'type'             => 'select',
		'show_option_none' => false,
		'default'          => '',
		'options'          => array(
			''   => __( 'Yes', 'wp-food' ),
			'no' => __( 'No', 'wp-food' ),	
		),
	) );
	$adv_options->add_field( array(
		'name'       => esc_html__( 'Billing Note required', 'wp-food' ),
		'desc'       => esc_html__( 'Select yes to make this field is required', 'wp-food' ),
		'id'         => 'exfood_ck_note',
		'type'             => 'select',
		'show_option_none' => false,
		'default'          => '',
		'options'          => array(
			''   => __( 'Yes', 'wp-food' ),
			'no' => __( 'No', 'wp-food' ),	
		),
	) );
	$adv_options->add_field( array(
		'name'       => esc_html__( 'Date select required', 'wp-food' ),
		'desc'       => esc_html__( 'Select yes to make this field is required', 'wp-food' ),
		'id'         => 'exfood_ck_date',
		'type'             => 'select',
		'show_option_none' => false,
		'default'          => '',
		'options'          => array(
			''   => __( 'Yes', 'wp-food' ),
			'no' => __( 'No', 'wp-food' ),	
		),
	) );
	$adv_options->add_field( array(
		'name'       => esc_html__( 'Time select required', 'wp-food' ),
		'desc'       => esc_html__( 'Select yes to make this field is required', 'wp-food' ),
		'id'         => 'exfood_ck_time',
		'type'             => 'select',
		'show_option_none' => false,
		'default'          => '',
		'options'          => array(
			''   => __( 'Yes', 'wp-food' ),
			'no' => __( 'No', 'wp-food' ),	
		),
	) );
	$adv_options->add_field( array(
		'name'       => esc_html__( 'Time option', 'wp-food' ),
		'desc'       => esc_html__( 'Select yes to make this field is required', 'wp-food' ),
		'id'         => 'exfood_ck_times',
		'type' => 'text',
		'repeatable'     => true,
		'show_option_none' => false,
		'default'          => '',
		
	) );
	$adv_options->add_field( array(
		'name'       => esc_html__( 'Enable reCAPTCHA', 'wp-food' ),
		'desc'       => esc_html__( 'Select yes to Enable google reCAPTCHA', 'wp-food' ),
		'id'         => 'exfood_ck_captcha',
		'type'             => 'select',
		'show_option_none' => false,
		'default'          => '',
		'options'          => array(
			''   => __( 'Yes', 'wp-food' ),
			'no' => __( 'No', 'wp-food' ),	
		),
	) );
	$adv_options->add_field( array(
		'name' => esc_html__('reCAPTCHA Site Key','wp-food'),
		'desc' => esc_html__('Enter google reCAPTCHA Site Key','wp-food'),
		'id'   => 'exfood_captcha_key',
		'type'        => 'text', 
	) );
	$adv_options->add_field( array(
		'name' => esc_html__('reCAPTCHA Secret Key','wp-food'),
		'desc' => esc_html__('Enter google reCAPTCHA Secret Key','wp-food'),
		'id'   => 'exfood_captcha_secret',
		'type'        => 'text', 
	) );
	/**
	 * Registers secondary options page, and set main item as parent.
	 */
	$args = array(
		'id'           => 'exfood_custom_code',
		'menu_title'   => '',
		'object_types' => array( 'options-page' ),
		'option_key'   => 'exfood_custom_code_options',
		'parent_slug'  => 'edit.php?post_type=ex_food',
		'tab_group'    => 'exfood_options',
		'tab_title'    => esc_html__('Custom Code','wp-food'),
	);
	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'exfood_options_display_with_tabs';
	}
	$customcode_options = new_cmb2_box( $args );
	$customcode_options->add_field( array(
		'name' => esc_html__('Custom Css','wp-food'),
		'desc' => esc_html__('Paste your custom Css code','wp-food'),
		'id'   => 'exfood_custom_css',
		'type' => 'textarea_code',
	) );
	$customcode_options->add_field( array(
		'name' => esc_html__('Custom Js','wp-food'),
		'desc' => esc_html__('Paste your custom Js code','wp-food'),
		'id'   => 'exfood_custom_js',
		'type' => 'textarea_code',
	) );
	/**
	 * Registers tertiary options page, and set main item as parent.
	 */
	$args = array(
		'id'           => 'exfood_js_css_file',
		'menu_title'   => '',
		'object_types' => array( 'options-page' ),
		'option_key'   => 'exfood_js_css_file_options',
		'parent_slug'  => 'edit.php?post_type=ex_food',
		'tab_group'    => 'exfood_options',
		'tab_title'    => esc_html__('Js + Css file','wp-food'),
	);
	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'exfood_options_display_with_tabs';
	}
	$file_options = new_cmb2_box( $args );
	$file_options->add_field( array(
		'name'             => esc_html__( 'Turn off Google Font', 'wp-food' ),
		'desc'             => esc_html__( 'Turn off loading Google Font', 'wp-food' ),
		'id'               => 'exfood_disable_ggfont',
		'type'             => 'select',
		'show_option_none' => false,
		'options'          => array(
			'' => esc_html__( 'No', 'wp-food' ),
			'yes'   => esc_html__( 'Yes', 'wp-food' ),
		),
	) );
}
add_action( 'cmb2_admin_init', 'exfood_register_setting_options' );

function exfood_hide_if_disable_single( $field ) {
	if ( exfood_get_option('exfood_disable_single') =='yes' ) {
		return false;
	}
	return true;
}
/**
 * A CMB2 options-page display callback override which adds tab navigation among
 * CMB2 options pages which share this same display callback.
 *
 * @param CMB2_Options_Hookup $cmb_options The CMB2_Options_Hookup object.
 */
function exfood_options_display_with_tabs( $cmb_options ) {
	$tabs = exfood_options_page_tabs( $cmb_options );
	?>
	<div class="wrap cmb2-options-page option-<?php echo esc_attr($cmb_options->option_key); ?>">
		<?php if ( get_admin_page_title() ) : ?>
			<h2><?php echo wp_kses_post( get_admin_page_title() ); ?></h2>
		<?php endif; ?>
		<h2 class="nav-tab-wrapper">
			<?php foreach ( $tabs as $option_key => $tab_title ) : ?>
				<a class="nav-tab<?php if ( isset( $_GET['page'] ) && $option_key === $_GET['page'] ) : ?> nav-tab-active<?php endif; ?>" href="<?php menu_page_url( $option_key ); ?>"><?php echo wp_kses_post( $tab_title ); ?></a>
			<?php endforeach; ?>
		</h2>
		<form class="cmb-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST" id="<?php echo esc_attr($cmb_options->cmb->cmb_id); ?>" enctype="multipart/form-data" encoding="multipart/form-data">
			<input type="hidden" name="action" value="<?php echo esc_attr( $cmb_options->option_key ); ?>">
			<?php $cmb_options->options_page_metabox(); ?>
			<?php submit_button( esc_attr( $cmb_options->cmb->prop( 'save_button' ) ), 'primary', 'submit-cmb' ); ?>
		</form>
	</div>
	<?php
}
/**
 * Gets navigation tabs array for CMB2 options pages which share the given
 * display_cb param.
 *
 * @param CMB2_Options_Hookup $cmb_options The CMB2_Options_Hookup object.
 *
 * @return array Array of tab information.
 */
function exfood_options_page_tabs( $cmb_options ) {
	$tab_group = $cmb_options->cmb->prop( 'tab_group' );
	$tabs      = array();
	foreach ( CMB2_Boxes::get_all() as $cmb_id => $cmb ) {
		if ( $tab_group === $cmb->prop( 'tab_group' ) ) {
			$tabs[ $cmb->options_page_keys()[0] ] = $cmb->prop( 'tab_title' )
				? $cmb->prop( 'tab_title' )
				: $cmb->prop( 'title' );
		}
	}
	return $tabs;
}

add_action( 'save_post', 'ex_food_ceate_product',1 );
if(!function_exists('ex_food_ceate_product')){
	function ex_food_ceate_product($post_id){
		if('ex_food' != get_post_type()){
			return;
		}
		if(isset($_POST['exfood_product']) && is_numeric($_POST['exfood_product'])){
			$_product = wc_get_product( $_POST['exfood_product'] );
			$wsprice = $_product->get_sale_price();
			$wprice = $_product->get_price();
			$wsku = $_product->get_sku();
			if(isset($_POST['woo_price']) && $_POST['woo_price']!= $wprice && is_numeric($_POST['woo_price'])){
				update_post_meta( $_POST['exfood_product'], '_regular_price', $_POST['woo_price']);
				update_post_meta( $_POST['exfood_product'], '_price', $_POST['woo_price']);
			}
			if(isset($_POST['woo_sprice']) && $_POST['woo_sprice']!= $wsprice && is_numeric($_POST['woo_sprice'])){
				update_post_meta( $_POST['exfood_product'], '_sale_price', $_POST['woo_sprice']);
			}
			if(isset($_POST['woo_sku'])){
				update_post_meta( $_POST['exfood_product'], '_sku', $_POST['woo_sku']);
			}		
		}else if(isset($_POST['woo_price']) && !is_numeric($_POST['exfood_product'])){
			$woo = array(
				'post_content'   => '',
				'post_name' 	   => sanitize_title($_POST['woo_name']),
				'post_title'     => $_POST['woo_name'],
				'post_status'    => 'pending',
				'post_type'      => 'product'
			);
			if($new_product = wp_insert_post( $woo, false )){
				add_post_meta( $new_product, '_is_food_product', 1);
				add_post_meta( $new_product, '_regular_price', $_POST['woo_price']);
				add_post_meta( $new_product, '_price', $_POST['woo_price']);
				add_post_meta( $new_product, '_sale_price', $_POST['woo_sprice']);
				add_post_meta( $new_product, '_sku', $_POST['woo_sku']);
				update_post_meta( $post_id, 'exfood_product', $new_product);
			}	
		}
	}
}
