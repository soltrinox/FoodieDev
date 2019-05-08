<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package storefront
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=2.0">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<!--<meta http-equiv="refresh" content="30" />-->
<?php wp_head(); ?>
    <link rel="stylesheet" href="https://www.aichart.io/options/css/normalize.min.css">
    <link rel="stylesheet" href="https://www.aichart.io/options/css/animate.min.css">
    <link rel="stylesheet" href="https://www.aichart.io/options/css/optionsmenu.css">
    <link rel="stylesheet" href="https://cdn.metroui.org.ua/v4/css/metro-all.min.css">
</head>

<body <?php body_class(); ?>>

<?php do_action( 'storefront_before_site' ); ?>

<div id="page" class="hfeed site">
	<?php do_action( 'storefront_before_header' ); ?>

	<header id="masthead" class="site-header" role="banner" style="<?php storefront_header_styles(); ?>">

		<?php
		/**
		 * Functions hooked into storefront_header action
		 *
		 * @hooked storefront_header_container                 - 0
		 * @hooked storefront_skip_links                       - 5
		 * @hooked storefront_social_icons                     - 10
		 * @hooked storefront_site_branding                    - 20
		 * @hooked storefront_secondary_navigation             - 30
		 * @hooked storefront_product_search                   - 40
		 * @hooked storefront_header_container_close           - 41
		 * @hooked storefront_primary_navigation_wrapper       - 42
		 * @hooked storefront_primary_navigation               - 50
		 * @hooked storefront_header_cart                      - 60
		 * @hooked storefront_primary_navigation_wrapper_close - 68
		 */
		do_action( 'storefront_header' );
		?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tween.js/16.3.5/Tween.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
        <script
                src="https://code.jquery.com/jquery-3.3.1.min.js"
                integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
                crossorigin="anonymous"></script>
	</header><!-- #masthead -->

	<?php
	/**
	 * Functions hooked in to storefront_before_content
	 *
	 * @hooked storefront_header_widget_region - 10
	 * @hooked woocommerce_breadcrumb - 10
	 */
	do_action( 'storefront_before_content' );
	?>

    <div style="display:none" class="site-content">
        <div class="col-full content-area">SHOP CMD ACTIONS </div>
        <div id="app" style="" class="flex flex-column">
            <div class="flex flex-column">
                <div class="shopcmd flex flex-column flex-1 clear">
                </div>
            </div>
        </div>
    </div>

    <!--   ############## START OPTIONS MENU #####################  -->

    <ul style="display: none;">
        <li><a id="demo02" href="#modal-02">Options Menu2</a></li>
    </ul>

    <div id="modal-02">
        <div  id="btn-close-modal" class="close-modal-02">
            X CLOSE MODAL X
        </div>

        <div class="modal-content">
            <div id="contentcards" class="contentcards">

            </div>
            <div class="selectMessage"><span style="margin-top: 30px;">Voice Select The Corresponding Option Number</span></div>
            <div><button class="pageOptions" id="nextGroup">NEXT OPTIONS GROUP</button></div>
        </div>
    </div>


    <!--   ################  END OPTIONS MENU ###################  -->

	<div id="content" class="site-content" tabindex="-1">
		<div class="col-full">

		<?php
		do_action( 'storefront_content_top' );
