<?php
function exfood_custom_css(){
    ob_start();
    $exfood_color = exfood_get_option('exfood_color');

    $hex  = str_replace("#", "", $exfood_color);
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
    if($exfood_color!=''){
    	?>

        .ex-fdlist .exstyle-1 figcaption .exstyle-1-button,
        .exfood-woocommerce.woocommerce form.cart button[type="submit"],
        .exfood-woocommerce.woocommerce .cart:not(.grouped_form) .quantity input[type=button],
        .ex-fdlist .exstyle-2 figcaption .exstyle-2-button,
        .ex-fdlist .exstyle-3 figcaption .exstyle-3-button,
        .ex-fdlist .exstyle-4 figcaption h5,
        .ex-fdlist .exstyle-4 .exfd-icon-plus:before,
        .ex-fdlist .exstyle-4 .exfd-icon-plus:after,
        .exstyle-button-bin,
        .exfd-table-1 .exfood-buildin-cart input[name="food_qty"],
        .exfd-table-1 .ex-fd-table-order .exfd-icon-plus:before,
        .exfd-table-1 .ex-fd-table-order .exfd-icon-plus:after,
        .ex-loadmore .loadmore-exfood:hover,
        .exfood-quantity input[type="button"],
        .exfd-cart-content .exfd-close-cart,
        .exfd-cart-content .woocommerce-mini-cart__buttons a,
        .ex-fdlist .exstyle-4 figcaption .exbt-inline .exstyle-4-button,
        .ex_close,
        .exfood-mulit-steps >div.active,
        .exfood-cart-shortcode.exfd-cart-content .exfd-cart-buildin ul li.exfood-cart-header,
        .exfd-user-order .exfd-table-order thead th,
        .exfd-user-main .exfd-user-content h3,
        .ex-fdlist.category_left .exfd-filter .ex-menu-list .ex-active-left:after{background:<?php echo esc_attr($exfood_color);?>;}

        .ex-fdlist .exfd-filter .exfd-filter-group .ex-menu-list .ex-menu-item-active{
            background:<?php echo esc_attr($exfood_color);?>;
            border-color:<?php echo esc_attr($exfood_color);?>;
        }
        .ex-fdlist .exfd-filter .exfd-filter-group .ex-menu-list .ex-menu-item-active:after,
        .ex-fdlist .exstyle-4 figcaption{
            border-top-color: <?php echo esc_attr($exfood_color);?>;
        }
        .fdstyle-list-1 .fdlist_1_des button,
        .fdstyle-list-2 .fdlist_2_title .fdlist_2_price button,
        .fdstyle-list-3 .fdlist_3_order button,
        .ex-fdlist .exstyle-4 figcaption .exstyle-4-button.exfd-choice,
        .exfd-table-1 .ex-fd-table-order button{
            border-color: <?php echo esc_attr($exfood_color);?>;
        }
        .ex-fdlist.style-4 .item-grid{
            border-bottom-color: <?php echo esc_attr($exfood_color);?>;
        }
        .exfood-mulit-steps >div.active:after {
            border-left-color: <?php echo esc_attr($exfood_color);?>;
        }
        .ex-fdlist .exstyle-1 figcaption h5,
        .ex-fdlist .exstyle-2 figcaption h5,
        .ex-fdlist .exstyle-3 figcaption h5,
        .exfd-table-1 td.ex-fd-name h3 a,
        .fdstyle-list-1 .fdlist_1_title .fdlist_1_price,
        .ex-fdlist .ex-popup-location .ex-popup-content .ex-popup-info h1,
        .ex-fdlist.category_left .exfd-filter .ex-menu-list a:hover,
        .ex-fdlist.category_left .exfd-filter .ex-menu-list .ex-active-left,
        .ex-fdlist.ex-fdcarousel .ex_s_lick-dots li.ex_s_lick-active button:before,
        .ex-fdlist.ex-fdcarousel .ex_s_lick-dots li button:before{
            color: <?php echo esc_attr($exfood_color);?>;
        }
        .exfd-pagination .page-navi .page-numbers.current {
            background-color: <?php echo esc_attr($exfood_color);?>;
            border-color: <?php echo esc_attr($exfood_color);?>;
        }
        .ex-loadmore .loadmore-exfood{
            border-color: <?php echo esc_attr($exfood_color);?>;
            color: <?php echo esc_attr($exfood_color);?>;
        }
        .ex-loadmore .loadmore-exfood span:not(.load-text),
        .ex-fdlist .exfd-shopping-cart,
        .fdstyle-list-1 .exfd-icon-plus:before,
        .fdstyle-list-1 .exfd-icon-plus:after,
        .fdstyle-list-2 .exfd-icon-plus:before,
        .fdstyle-list-3 .exfd-icon-plus:before,
        .fdstyle-list-2 .exfd-icon-plus:after,
        .fdstyle-list-3 .exfd-icon-plus:after,
        .exfd-table-1 th{
            background-color: <?php echo esc_attr($exfood_color);?>;
        }
        @media screen and (max-width: 768px){

        }
        @media screen and (max-width: 992px) and (min-width: 769px){

        }
        <?php
    }
    $exfood_font_family = exfood_get_option('exfood_font_family');
    $main_font_family = explode(":", $exfood_font_family);
    $main_font_family = $main_font_family[0];
    if($exfood_font_family!=''){?>
        .ex-fdlist,
        .sc-min-cart,
        .exfood-thankyou,
        .exfood-cart-shortcode, .exfood-checkout-shortcode{font-family: "<?php echo esc_html($main_font_family);?>", sans-serif;}
        <?php
    }
    $exfood_font_size = exfood_get_option('exfood_font_size');
    if($exfood_font_size!=''){?>
        .ex-fdlist,
        .sc-min-cart,
        .exfood-thankyou,
        .exfood-cart-shortcode, .exfood-checkout-shortcode{font-size: <?php echo esc_html($exfood_font_size);?>;}
        <?php
    }
    $exfood_ctcolor = exfood_get_option('exfood_ctcolor');
    if($exfood_ctcolor!=''){?>
    	.ex-fdlist,
        .exfood-cart-shortcode, .exfood-checkout-shortcode,
        .exfd-table-1 td{color: <?php echo esc_html($exfood_ctcolor);?>;}
        <?php
    }

    $exfood_headingfont_family = exfood_get_option('exfood_headingfont_family');
    $h_font_family = explode(":", $exfood_headingfont_family);
    $h_font_family = $h_font_family[0];
    if($h_font_family!=''){?>
    	.ex-fdlist .exstyle-1 h3 a,
        .ex-fdlist .exstyle-2 h3 a,
        .ex-fdlist .exstyle-3 h3 a,
        .ex-fdlist .exstyle-4 h3 a,
        .exfood-thankyou h3,
        .ex-popup-location .ex-popup-content .ex-popup-info h1,
        .exfd-table-1 td.ex-fd-name h3 a,
        .fdstyle-list-1 .fdlist_1_title .fdlist_1_name,
        .fdstyle-list-2 .fdlist_2_title .fdlist_2_name,
        .fdstyle-list-3 .fdlist_3_title h3,
        .ex_modal .modal-content .fd_modal_des h3,
        .ex-fdlist .exfd-filter .exfd-filter-group .ex-menu-list a,
        .ex-fdlist .exfd-filter .exfd-filter-group .ex-menu-select,
        .ex-fdlist .exfd-filter .exfd-filter-group .ex-menu-select select{
            font-family: "<?php echo esc_html($h_font_family);?>", sans-serif;
        }
    	<?php 
    }
    $exfood_headingfont_size = exfood_get_option('exfood_headingfont_size');
    if($exfood_headingfont_size!=''){?>
    	.ex-fdlist .exstyle-1 h3 a,
        .ex-fdlist .exstyle-2 h3 a,
        .ex-fdlist .exstyle-3 h3 a,
        .ex-fdlist .exstyle-4 h3 a,
        .exfood-thankyou h3,
        .ex-popup-location .ex-popup-content .ex-popup-info h1,
        .exfd-table-1 td.ex-fd-name h3 a,
        .fdstyle-list-1 .fdlist_1_title .fdlist_1_name,
        .fdstyle-list-2 .fdlist_2_title .fdlist_2_name,
        .fdstyle-list-3 .fdlist_3_title h3,
        .ex-fdlist .exfd-filter .exfd-filter-group .ex-menu-list a,
        .ex-fdlist .exfd-filter .exfd-filter-group .ex-menu-select select{font-size: <?php echo esc_html($exfood_headingfont_size);?>;}
        <?php
    }
    $exfood_hdcolor = exfood_get_option('exfood_hdcolor');
    if($exfood_hdcolor!=''){?>
    	.ex-fdlist .exstyle-1 h3 a,
        .ex-fdlist .exstyle-2 h3 a,
        .ex-fdlist .exstyle-4 h3 a,
        .ex-popup-location .ex-popup-content .ex-popup-info h1,
        .ex-fdlist .exfd-filter .exfd-filter-group .ex-menu-list a,
        .ex_modal .modal-content .fd_modal_des h3,
        .fdstyle-list-1 .fdlist_1_title .fdlist_1_name,
        .fdstyle-list-2 .fdlist_2_title .fdlist_2_name,
        .fdstyle-list-3 .fdlist_3_title h3,
        .exfd-table-1 td.ex-fd-name h3 a,
        .ex-fdlist .exfd-filter .exfd-filter-group .ex-menu-select select{color: <?php echo esc_html($exfood_hdcolor);?>;}
        <?php
    }
    // price font
    $exfood_pricefont_family = exfood_get_option('exfood_pricefont_family');
    $price_font_family = explode(":", $exfood_pricefont_family);
    $price_font_family = $price_font_family[0];
    if($price_font_family!=''){?>
        .ex-fdlist .exstyle-1 figcaption h5,
        .ex-fdlist .exstyle-2 figcaption h5,
        .ex-fdlist .exstyle-3 figcaption h5,
        .ex-fdlist .exstyle-4 figcaption h5,
        .exfd-table-1 td .exfd-price-detail,
        .fdstyle-list-1 .fdlist_1_title .fdlist_1_price,
        .fdstyle-list-2 .fdlist_2_title .fdlist_2_price,
        .ex_modal .modal-content .fd_modal_des h5{
            font-family: "<?php echo esc_html($price_font_family);?>", sans-serif;
        }
        <?php 
    }
    $exfood_pricefont_size = exfood_get_option('exfood_pricefont_size');
    if($exfood_pricefont_size!=''){?>
        .ex-fdlist .exstyle-1 figcaption h5,
        .ex-fdlist .exstyle-2 figcaption h5,
        .ex-fdlist .exstyle-3 figcaption h5,
        .ex-fdlist .exstyle-4 figcaption h5,
        .exfd-table-1 td .exfd-price-detail,
        .fdstyle-list-1 .fdlist_1_title .fdlist_1_price,
        .fdstyle-list-2 .fdlist_2_title .fdlist_2_price,
        .ex_modal .modal-content .fd_modal_des h5{font-size: <?php echo esc_html($exfood_pricefont_size);?>;}
        <?php
    }
    $exfood_pricecolor = exfood_get_option('exfood_pricecolor');
    if($exfood_pricecolor!=''){?>
        .ex-fdlist .exstyle-1 figcaption h5,
        .ex-fdlist .exstyle-2 figcaption h5,
        .ex-fdlist .exstyle-3 figcaption h5,
        .ex-fdlist .exstyle-4 figcaption h5,
        .exfd-table-1 td .exfd-price-detail,
        .fdstyle-list-1 .fdlist_1_title .fdlist_1_price,
        .fdstyle-list-2 .fdlist_2_title .fdlist_2_price,
        .ex_modal .modal-content .fd_modal_des h5{color: <?php echo esc_html($exfood_pricecolor);?>;}
        <?php
    }
    // end price font


    $exfood_metafont_family = exfood_get_option('exfood_metafont_family');
    $m_font_family = explode(":", $exfood_metafont_family);
    $m_font_family = $m_font_family[0];
    if($m_font_family!=''){?>
    	.ex_modal .modal-content .fd_modal_des .exfd_nutrition li{
            font-family: "<?php echo esc_html($m_font_family);?>", sans-serif;
        }
    	<?php 
    }
    $exfood_metafont_size = exfood_get_option('exfood_metafont_size');
    if($exfood_metafont_size!=''){?>
    	.ex_modal .modal-content .fd_modal_des .exfd_nutrition li{font-size: <?php echo esc_html($exfood_metafont_size);?>;}
        <?php
    }
    $exfood_mtcolor = exfood_get_option('exfood_mtcolor');
    if($exfood_mtcolor!=''){?>
    	.ex_modal .modal-content .fd_modal_des .exfd_nutrition li{color: <?php echo esc_html($exfood_mtcolor);?>;}
        <?php
    }
    ?>
    select.ex-ck-select,.exfood-select-loc select.ex-loc-select{background-image: url(<?php echo esc_url(EXFOOD_PATH.'css/icon-dropdow.png');?>);}
    <?php

    $exfood_custom_css = exfood_get_option('exfood_custom_css','exfood_custom_code_options');
    if($exfood_custom_css!=''){
    	echo wp_kses_post($exfood_custom_css);
    }
    $output_string = ob_get_contents();
    ob_end_clean();
    return $output_string;
}