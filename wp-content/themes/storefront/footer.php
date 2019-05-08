<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package storefront
 */

?>

		</div><!-- .col-full -->
	</div><!-- #content -->

	<?php do_action( 'storefront_before_footer' ); ?>

	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="col-full">

			<?php
			/**
			 * Functions hooked in to storefront_footer action
			 *
			 * @hooked storefront_footer_widgets - 10
			 * @hooked storefront_credit         - 20
			 */
			do_action( 'storefront_footer' );
			?>

		</div><!-- .col-full -->
	</footer><!-- #colophon -->

	<?php do_action( 'storefront_after_footer' ); ?>

</div><!-- #page -->

<?php wp_footer(); ?>

<script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="https://cdn.metroui.org.ua/v4/js/metro.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/lodash@4.17.11/lodash.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.12.0/moment.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.4/socket.io.js"></script>
<script type="text/javascript" src="//unpkg.com/@feathersjs/client@^3.0.0/dist/feathers.js"></script>
<script type="text/javascript" src="//unpkg.com/feathers-memory@^2.0.0/dist/feathers-memory.js"></script>

<script type="text/javascript" src="https://www.aichart.io/options/js/animatedModal.js"></script>
<script type="text/javascript" src="https://www.aichart.io/shopping.js"></script>
</body>
</html>
