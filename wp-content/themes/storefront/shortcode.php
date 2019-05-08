<?php

// +----------------------------------------------------------------------+
// | Copyright 2014  Madpixels  (email : contact@madpixels.net)           |
// +----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License, version 2, as  |
// | published by the Free Software Foundation.                           |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
// | GNU General Public License for more details.                         |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to the Free Software          |
// | Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,               |
// | MA 02110-1301 USA                                                    |
// +----------------------------------------------------------------------+
// | Author: Eugene Manuilov <eugene.manuilov@gmail.com>                  |
// +----------------------------------------------------------------------+

// prevent direct access
if ( !defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 404 Not Found', true, 404 );
	exit;
}

// add action hooks
add_action( 'wp_enqueue_scripts', 'wccm_enqueue_shortcode_scripts' );
// add shortcode hooks
add_shortcode( 'products-compare', 'wccm_compare_shortcode' );

/**
 * Enqueues scripts and styles for shortcode.
 *
 * @since 1.1.0
 * @action wp_enqueue_scripts
 */
function wccm_enqueue_shortcode_scripts() {
	if ( is_single() || is_page() ) {
		$post = get_queried_object();
		if ( is_a( $post, 'WP_Post' ) && preg_match( '/\[products-compare.*?\]/is', $post->post_content ) ) {
			wp_enqueue_style( 'wccm-compare' );
			wp_enqueue_script( 'wccm-compare' );
		}
	}
}

/**
 * Renders compare list shortcode.
 *
 * @since 1.1.0
 * @shortcode products-compare
 *
 * @param array $atts The array of shortcode attributes.
 * @param string $content The shortcode content.
 */
function wccm_compare_shortcode( $atts, $content = '' ) {
	$atts = shortcode_atts( array( 'ids' => '', 'atts' => '' ), $atts, 'products-compare' );

	$list = wp_parse_id_list( $atts['ids'] );
	if ( !empty( $list ) ) {
		$attributes = array_filter( array_map( 'trim', explode( ',', $atts['atts'] ) ) );
		return wccm_compare_list_render( $list, $attributes );
	}

	return $content;
}

/**
 * Renders compare list table.
 *
 * @since 1.1.0
 *
 * @param array $list The array of compare products.
 * @param array $attributes The array of attributes to show in the table.
 * @return string Compare table HTML.
 */
function wccm_compare_list_render( $list, $attributes = array() ) {
	$products = array();
	foreach ( $list as $product_id ) {
		$product = wc_get_product( $product_id );
		if ( $product ) {
			$products[$product_id] = $product;
		}
	}

	$content = '';
	if ( !empty( $products ) ) {
		ob_start();
			echo '<div id="productListArray" class="wccm-compare-table">';
				wccm_compare_list_render_header( $products );
				wccm_compare_list_render_attributes( $products, $attributes );
			echo '</div>';
			$content = ob_get_contents();
		ob_end_clean();
	}

	return $content;
}

/**
 * Renders compare table header.
 *
 * @since 1.1.0
 *
 * @param array $products The compare items list.
 */
function wccm_compare_list_render_header( $products ) {
	echo '<div class="wccm-thead">';
		echo '<div class="wccm-tr">';
			echo '<div class="wccm-th">';
			echo '</div>';
			echo '<div class="wccm-table-wrapper">';
				echo '<table class="wccm-table" cellspacing="0" cellpadding="0" border="0">';
					echo '<tr>';
					$ctt = 1;
					$listToCompare = [];
						foreach ( $products as $product_id => $product ) {
							echo '<td class="wccm-td">';
								echo '<div class="wccm-thumb" id="SearchItemThumb',  $product_id  ,'"  >';
                                echo '<h4 class="ml-4" style="height: 2px">&nbsp;<span class="badge bg-black fg-white" style="margin-top:20px; margin-right:20px; "  name="productBadge" >' . $ctt . '</span></h4>';
//									echo '<a class="dashicons dashicons-no" href="', wccm_get_compare_link( $product_id, 'remove-from-list' ), '"></a>';
									echo get_the_post_thumbnail( $product_id, 'post-thumbnail'  );
								echo '</div>';
								echo '<div id="compareItemBox',  $product_id  ,'">';
									echo '<a id="compareItemLink',  $product_id  ,'" href="', get_permalink( $product_id ), '" >' , $product->get_title(), '';
                                    echo '</a>';
								echo '</div>';
								echo '<div class="price">';
									echo $product->get_price_html();
								echo '</div>';
							echo '</td>';
							$ctt++;
						}
					echo '<tr>';
				echo '</table>';
				echo '<div id="ProductCompareBox"><ul id="ProductCompareArray" style="display:block;"  >';
                foreach ( $products as $product_id => $product ) {
                    /*  this is the unique identifier on the LI  */
                    echo '<li id="SearchItemNumber',  $product_id  ,'"  name="SearchItemElements" >';

                    $titProd = $product->get_title();
                    echo '<a name="productItemLink" id="compareItem',  $product_id  ,'" >', $product_id, ' ] ' , substr( $titProd, 0 , 20)  ;
                    echo '<br /><span id="compareItemTitle',  $product_id  ,'"  >' , $product->get_title(), '</span>';
                    echo '<br /><span id="compareItemDesc',  $product_id  ,'" >', $product->get_short_description()  ,'</span>';
                    echo '</a>';
                    echo '</li>';
                }
                echo '</ul></div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
}

/**
 * Renders compare table attributes.
 *
 * @since 1.1.0
 *
 *
 * <li id="SearchItemNumber2575" value="_0" name="SearchItemElements" class="post-2575 product type-product status-publish has-post-thumbnail product_cat-home-and-kitchen product_cat-kos-kitchen first instock sale shipping-taxable purchasable product-type-simple pr-2">

<a name="productItemLink" id="product-2575" href="http://speak2.shop:81/wordpress/product/breville-bes880bss-barista-touch-espresso-maker-stainless-steel/" class="woocommerce-LoopProduct-link woocommerce-loop-product__link"><img width="324" height="324" src="https://images-na.ssl-images-amazon.com/images/I/41IPPYvLjEL._SS324_.jpg" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail wp-post-image" alt="" srcset="https://images-na.ssl-images-amazon.com/images/I/41IPPYvLjEL._SS324_.jpg 324w, https://images-na.ssl-images-amazon.com/images/I/41IPPYvLjEL._SS150_.jpg 150w, https://images-na.ssl-images-amazon.com/images/I/41IPPYvLjEL._SS300_.jpg 300w, https://images-na.ssl-images-amazon.com/images/I/41IPPYvLjEL._SS768_.jpg 768w, https://images-na.ssl-images-amazon.com/images/I/41IPPYvLjEL._SS1024_.jpg 1024w, https://images-na.ssl-images-amazon.com/images/I/41IPPYvLjEL._SS416_.jpg 416w, https://images-na.ssl-images-amazon.com/images/I/41IPPYvLjEL._SS100_.jpg 100w, https://images-na.ssl-images-amazon.com/images/I/41IPPYvLjEL.jpg 500w" sizes="(max-width: 324px) 100vw, 324px"><h2 name="itemProductTitle" id="title2575" class="woocommerce-loop-product__title">Breville BES880BSS Barista Touch Espresso Maker, Stainless Steel</h2><span name="itemShortDescription" id="desc2575" style="width:0px;height: 0px; overflow: hidden; display: none" class="itemShortDescription">CLEVER. AUTOMATIC. CUSTOMIZE: Intuitive touch screen display simplifies how to make your favorite café coffee in 3 easy steps - Grind, Brew and Milk. You can easily adjust the coffee strength, milk texture and temperature to suit your taste. Then save the setting with your own unique name. Create and save up to 8 personalized coffees.
AUTOMATIC MICRO-FOAM MILK TEXTURING: Auto steam wand, allows you to adjust the milk temperature and texture to suit your taste. Delivering barista quality micro-foam that enhances the flavor of the coffee and is essential for creating latté art</span>
<span class="onsale">Sale!</span>

<span class="price"><del><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>1,799.95</span></del> <ins><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>989.99</span></ins></span>
</a><a href="/wordpress/product-category/kos-kitchen/?add-to-cart=2575" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="2575" data-product_sku="" aria-label="Add “Breville BES880BSS Barista Touch Espresso Maker, Stainless Steel” to your cart" rel="nofollow">Add to cart</a><a href="/wordpress/product-category/kos-kitchen/?wccm=add-to-list&amp;pid=2575&amp;nonce=917edf9b81" class="wccm-button button compare">Compare</a>    <h4 class="ml-4" style="height: 2px">&nbsp;<span class="badge bg-black fg-white" style="margin-top:20px;" id="productIDLoopBadge2575" name="productBadge">1</span></h4>
</li>
 *
 *
 *
 *
 * @param array $products The compare items list.
 * @param array $selected_attributes The array of attributes to show in the table.
 */
function wccm_compare_list_render_attributes( $products, $selected_attributes = array() ) {
	$attributes = array();
	$empty_selected = empty( $selected_attributes );
	foreach ( $products as $product ) {
		foreach ( $product->get_attributes() as $attribute_id => $attribute ) {
			if ( $empty_selected || in_array( substr( $attribute['name'], 3 ), $selected_attributes ) ) {
				if ( !isset( $attributes[$attribute_id] ) ) {
					$attributes[$attribute_id] = array(
						'name'     => $attribute['name'],
						'products' => array(),
					);
				}

				$attributes[$attribute_id]['products'][$product->get_id()] = $attribute['is_taxonomy']
					? wc_get_product_terms( $product->get_id(), $attribute['name'], array( 'fields' => 'names' ) )
					: array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
			}
		}
	}

	echo '<div class="wccm-tbody">';
		foreach ( $attributes as $attribute ) {
			echo '<div class="wccm-tr">';
				echo '<div class="wccm-th">';
					echo wc_attribute_label( $attribute['name'] );
				echo '</div>';
				echo '<div class="wccm-table-wrapper">';
					echo '<table class="wccm-table" cellspacing="0" cellpadding="0" border="0">';
						echo '<tr>';
							foreach ( $products as $product ) {
								echo '<td class="wccm-td">';

									$values = !empty( $attribute['products'][$product->get_id()] ) ? $attribute['products'][$product->get_id()] : array( '&#8212;' );
									echo apply_filters( 'woocommerce_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attribute, $values );
								echo '</td>';
							}
						echo '</tr>';
					echo '</table>';
				echo '</div>';
			echo '</div>';
		}
	echo '</div>';
}
