<?php
/*
Plugin Name: WooCommerce Compare List
Plugin URI: http://wordpress.org/plugins/woocommerce-compare-list/
Description: The plugin adds ability to compare some products of your WooCommerce driven shop.
Version: 1.1.2
Author: Themeisle
Author URI: http://themeisle.com
License: GPL v2.0 or later
License URI: http://www.opensource.org/licenses/gpl-license.php
*/

// prevent direct access
if ( !defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 404 Not Found', true, 404 );
	exit;
}

// add action hooks
add_action( 'plugins_loaded', 'wccm_launch' );
// activation and deactivation stuff
register_activation_hook( __FILE__, 'flush_rewrite_rules' );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

/**
 * Launches plugin if WooCommerce is active.
 *
 * @since 1.1.0
 * @action plugins_loaded
 *
 * @return boolean TRUE if launched successfully, otherwise FALSE.
 */
function wccm_launch() {
	if ( !class_exists( 'WooCommerce' ) || defined( 'WCCM_VERISON' ) ) {
		return false;
	}

	define( 'WCCM_VERISON', '1.1.2' );

	load_plugin_textdomain( 'wccm', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	require_once 'includes/common.php';
	require_once 'includes/widget.php';
	if ( is_admin() ) {
		require_once 'includes/settings.php';
	} else {
		require_once 'includes/shortcode.php';
		if ( intval( get_option( 'wccm_compare_page' ) ) > 0 ) {
			require_once 'includes/buttons.php';
			require_once 'includes/compare-page.php';
			require_once 'includes/catalog.php';
		}
	}

	return true;
}

/**
 * Loads wccm-style.css file on any page
 *
 * @since 1.1.2
 */
function wccm_enqueue_style() {
	wp_enqueue_style( 'wccm-style', plugins_url( 'css/wccm-style.css', __FILE__ ), array(), WCCM_VERISON );

	$current_theme_template = wp_get_theme()->template;

	wccm_style_for_hestia( $current_theme_template );

	wccm_style_for_zerif( $current_theme_template );

	wccm_style_for_shop_isle( $current_theme_template );

}
add_action( 'wp_enqueue_scripts', 'wccm_enqueue_style' );

/**
 * Compatibility with Hestia theme
 *
 * @since 1.1.2
 */
function wccm_style_for_hestia( $current_theme_template ) {

	if ( $current_theme_template !== 'hestia' && $current_theme_template !== 'hestia-pro' ) {
		return;
	}

	wp_enqueue_style( 'wccm-style-for-hestia', plugins_url( 'css/hestia.css', __FILE__ ), array(), WCCM_VERISON );

	$hestia_accent_color = get_theme_mod( 'accent_color' );

	if ( ! $hestia_accent_color ) {
		return;
	}

	$custom_css = '';

	/* Button and hover state background color */
	$custom_css .= '
		.woocommerce.archive .wccm-button,
		.woocommerce.archive .wccm-button:hover,
		.woocommerce.archive .wccm-catalog-item a.button,
		.woocommerce.archive .wccm-catalog-item a.button:hover,
		.woocommerce.single-product .wccm-button,
		.woocommerce.single-product .wccm-button:hover {
			background-color: ' . esc_html( $hestia_accent_color ) . ';
	}';

	if ( function_exists( 'hestia_hex_rgba' ) ) {
		/* Button's box-shadow */
		$custom_css .= '	
			.woocommerce.archive .wccm-button,
			.woocommerce.archive .wccm-catalog-item a.button,
			.woocommerce.single-product .wccm-button {
				-webkit-box-shadow: 0 2px 2px 0 ' . hestia_hex_rgba( $hestia_accent_color, '0.14' ) . ',0 3px 1px -2px ' . hestia_hex_rgba( $hestia_accent_color, '0.2' ) . ',0 1px 5px 0 ' . hestia_hex_rgba( $hestia_accent_color, '0.12' ) . ';
	    box-shadow: 0 2px 2px 0 ' . hestia_hex_rgba( $hestia_accent_color, '0.14' ) . ',0 3px 1px -2px ' . hestia_hex_rgba( $hestia_accent_color, '0.2' ) . ',0 1px 5px 0 ' . hestia_hex_rgba( $hestia_accent_color, '0.12' ) . ';
			}';

		/* Button's box-shadow on hover state */
		$custom_css .= '				
			.woocommerce.archive .wccm-button:hover,
			.woocommerce.archive .wccm-catalog-item a.button:hover,
			.woocommerce.single-product .wccm-button:hover {
				-webkit-box-shadow: 0 14px 26px -12px' . hestia_hex_rgba( $hestia_accent_color, '0.42' ) . ',0 4px 23px 0 rgba(0,0,0,0.12),0 8px 10px -5px ' . hestia_hex_rgba( $hestia_accent_color, '0.2' ) . ';
	    box-shadow: 0 14px 26px -12px ' . hestia_hex_rgba( $hestia_accent_color, '0.42' ) . ',0 4px 23px 0 rgba(0,0,0,0.12),0 8px 10px -5px ' . hestia_hex_rgba( $hestia_accent_color, '0.2' ) . ';
		color: #fff;
			}';
	}

	wp_add_inline_style( 'wccm-style', $custom_css );
}

/**
 * Compatibility with Zerif theme
 *
 * @since 1.1.2
 */
function wccm_style_for_zerif( $current_theme_template ) {

	if ( $current_theme_template !== 'zerif-lite' && $current_theme_template !== 'zerif-pro' ) {
		return;
	}

	wp_enqueue_style( 'wccm-style-for-zerif', plugins_url( 'css/zerif.css', __FILE__ ), array(), WCCM_VERISON );
}

/**
 * Compatibility with Shop Isle theme
 *
 * @since 1.1.2
 */
function wccm_style_for_shop_isle( $current_theme_template ) {

	if ( $current_theme_template !== 'shop-isle-pro' && $current_theme_template !== 'shop-isle' ) {
		return;
	}

	wp_enqueue_style( 'wccm-style-for-shop-isle', plugins_url( 'css/shop-isle.css', __FILE__ ), array(), WCCM_VERISON );

}