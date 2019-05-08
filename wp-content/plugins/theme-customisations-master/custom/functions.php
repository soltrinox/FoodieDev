<?php
/**
 * Functions.php
 *
 * @package  Theme_Customisations
 * @author   WooThemes
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function wc_product_class2( $class = '', $product_id = null ) {

    echo 'class="' . esc_attr( join( ' ', wc_get_product_class( $class, $product_id ) ) ) . ' pr-2" ';
}

function custom_remove_hook(){
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
    add_action( 'woocommerce_after_single_product_summary', 'custom_function_to_sort_related', 22 );
}
add_action( 'woocommerce_after_single_product_summary', 'custom_remove_hook' );

function custom_function_to_sort_related( $args = array() ) {
    global $product;

    if ( ! $product ) {
        return;
    }

    $defaults = array(
        'posts_per_page' => 4,
        'columns'        => 4,
        'orderby'        => 'price', // @codingStandardsIgnoreLine.
        'order'          => 'desc'
    );

    $args = wp_parse_args( $args, $defaults );

    // Get visible related products then sort them at random.
    $args['related_products'] = array_filter( array_map( 'wc_get_product', wc_get_related_products( $product->get_id(), $args['posts_per_page'], $product->get_upsell_ids() ) ), 'wc_products_array_filter_visible' );

    // Handle orderby.
    $args['related_products'] = wc_products_array_orderby( $args['related_products'], $args['orderby'], $args['order'] );

    // Set global loop values.
    wc_set_loop_prop( 'name', 'related' );
    wc_set_loop_prop( 'columns', apply_filters( 'woocommerce_related_products_columns', $args['columns'] ) );

    wc_get_template( 'single-product/related.php', $args );
}

/*
 *          wc_dropdown_variation_attribute_options
 */

remove_action( 'wc_dropdown_variation_attribute_options', 'wc_dropdown_variation_attribute_options', 20);
add_action( 'wc_dropdown_variation_attribute_options', 'wc_dropdown_variation_attribute_options', 22 );


if ( ! function_exists( 'wc_dropdown_variation_attribute_options' ) ) {

    /**
     * Output a list of variation attributes for use in the cart forms.
     *
     * @param array $args Arguments.
     * @since 2.4.0
     */
    function wc_dropdown_variation_attribute_options( $args = array() ) {
        $args = wp_parse_args( apply_filters( 'woocommerce_dropdown_variation_attribute_options_args', $args ), array(
            'options'          => false,
            'attribute'        => false,
            'product'          => false,
            'selected'         => false,
            'name'             => '',
            'id'               => '',
            'class'            => '',
            'show_option_none' => __( 'Choose an option', 'woocommerce' ),
        ) );

        // Get selected value.
        if ( false === $args['selected'] && $args['attribute'] && $args['product'] instanceof WC_Product ) {
            $selected_key     = 'attribute_' . sanitize_title( $args['attribute'] );
            $args['selected'] = isset( $_REQUEST[ $selected_key ] ) ? wc_clean( wp_unslash( $_REQUEST[ $selected_key ] ) ) : $args['product']->get_variation_default_attribute( $args['attribute'] ); // WPCS: input var ok, CSRF ok, sanitization ok.
        }

        $options               = $args['options'];
        $product               = $args['product'];
        $attribute             = $args['attribute'];
        $name                  = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
        $id                    = $args['id'] ? $args['id'] : sanitize_title( $attribute );
        $class                 = $args['class'];
        $show_option_none      = (bool) $args['show_option_none'];
        $show_option_none_text = $args['show_option_none'] ? $args['show_option_none'] : __( 'Choose an option', 'woocommerce' ); // We'll do our best to hide the placeholder, but we'll need to show something when resetting options.

        if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
            $attributes = $product->get_variation_attributes();
            $options    = $attributes[ $attribute ];
        }

        $html  = '<select id="' . esc_attr( $id ) . '" class="optionSelectMenu ' . esc_attr( $class ) . '" name="' . esc_attr( $name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '" data-show_option_none="' . ( $show_option_none ? 'yes' : 'no' ) . '">';
        $html .= '<option value=""  class="optionSelectItem"  id="option0">' . esc_html( $show_option_none_text ) . '</option>';

        /*

            Here we will create the values and template edits to support individual option ids and
            the

        */

        if ( ! empty( $options ) ) {
            if ( $product && taxonomy_exists( $attribute ) ) {
                // Get terms if this is a taxonomy - ordered. We need the names too.
                $terms = wc_get_product_terms( $product->get_id(), $attribute, array(
                    'fields' => 'all',
                ) );
                $optionNum = 1;
                foreach ( $terms as $term ) {
                    if ( in_array( $term->slug, $options, true ) ) {
                        $html .= '<option value="' . esc_attr( $term->slug ) . '" class="optionSelectItem" id="option'. $optionNum. '" ' . selected( sanitize_title( $args['selected'] ), $term->slug, false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) ) . '</option>';
                        $optionNum++;
                    }
                }
            } else {
                $optionNum = 1;
                foreach ( $options as $option ) {
                    // This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
                    $selected = sanitize_title( $args['selected'] ) === $args['selected'] ? selected( $args['selected'], sanitize_title( $option ), false ) : selected( $args['selected'], $option, false );
                    $html    .= '<option value="' . esc_attr( $option ) . '" class="optionSelectItem" id="option'. $optionNum. '" ' . $selected . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ) . '</option>';
                    $optionNum++;
                }
            }
        }

        $html .= '</select>';

        echo apply_filters( 'woocommerce_dropdown_variation_attribute_options_html', $html, $args ); // WPCS: XSS ok.
    }
}

/*
 *          woocommerce_template_loop_product_link_open
 */

remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 20 );
add_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 22 );

if ( ! function_exists( 'woocommerce_template_loop_product_link_open' ) ) {
    /**
     * Insert the opening anchor tag for products in the loop.
     */
    function woocommerce_template_loop_product_link_open() {
        global $product;

        $link = apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product );

        $pid = $product->get_id() ;

        echo '<a name="productItemLink"  id="product-' . $pid . '"  href="' . esc_url( $link ) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">';
    }
}

/*
 *
 */


remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 20 );
add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_link_open', 22 );

if ( ! function_exists( 'woocommerce_template_loop_product_title' ) ) {

    /**
     * Show the product title in the product loop. By default this is an H2.
     */
    function woocommerce_template_loop_product_title() {
        global $product;


        $pid = $product->get_id() ;

        echo '<h2 name="itemProductTitle" id="title'. $pid .'" class="woocommerce-loop-product__title">' .
            get_the_title() . '</h2>';
        echo '<span name="itemShortDescription"  id="desc'. $pid .'"  style="width:0px;height: 0px; overflow: hidden; display: none" class="itemShortDescription">'.
            $product->get_short_description()  .'</span>';
    }
}