;(function($){
	'use strict';
	$(document).ready(function() {
		/*-ready-*/
		if(jQuery('.post-type-exfood_scbd .cmb2-metabox select[name="sc_type"]').length>0){
			var $val = jQuery('.post-type-exfood_scbd .cmb2-metabox select[name="sc_type"]').val();
			if($val==''){
				$val ='grid';
			}
			if($val =='carousel'){
				jQuery('.cmb2-id-style select#style option').attr('disabled','disabled');
			}
			$('body').removeClass (function (index, className) {
				return (className.match (/(^|\s)ex-layout\S+/g) || []).join(' ');
			});
			$('body').addClass('ex-layout-'+$val);
			//$('.show-in'+$val).fadeIn();
			//$('.hide-in'+$val).fadeOut();
		}
		/*-on change-*/
		jQuery('.post-type-exfood_scbd .cmb2-metabox select[name="sc_type"]').on('change',function() {
			var $this = $(this);
			var $val = $this.val();
			if($val==''){
				$val ='grid';
			}
			if($val =='list'){
				jQuery('.post-type-exfood_scbd select#style option').attr('disabled','disabled');
				jQuery('.post-type-exfood_scbd select#style option[value="1"], .post-type-exfood_scbd select#style option[value="2"], .post-type-exfood_scbd select#style option[value="3"]').removeAttr("disabled");
			}else if($val =='table'){
				jQuery('.post-type-exfood_scbd select#style option').attr('disabled','disabled');
				jQuery('.post-type-exfood_scbd select#style option[value="1"]').removeAttr("disabled");
			}else{
				jQuery('.post-type-exfood_scbd select#style option').removeAttr('disabled','disabled');
			}
			$('body').removeClass (function (index, className) {
				return (className.match (/(^|\s)ex-layout\S+/g) || []).join(' ');
			});
			$('body').addClass('ex-layout-'+$val);
			//$('.show-in'+$val).fadeIn();
			//$('.hide-in'+$val).fadeOut();
			
		});
		/*-ajax save meta-*/
		jQuery('input[name="exfood_sort"]').on('change',function() {
			var $this = $(this);
			var post_id = $this.attr('data-id');
			var valu = $this.val();
           	var param = {
	   			action: 'exfood_change_sort_mb',
	   			post_id: post_id,
				value: valu
	   		};
	   		$.ajax({
	   			type: "post",
	   			url: exfood_ajax.ajaxurl,
	   			dataType: 'html',
	   			data: (param),
	   			success: function(data){
	   				return true;
	   			}	
	   		});
		});
		jQuery('.post-type-exfood_order input[name="exorder_food_id"]').on('change paste keyup',function() {
			$('.exfd-order-items').addClass('loading');
			var $this = $(this);
			var post_id = $('#post_ID').val();
			var food_id = $this.val();
			var param = {
	   			action: 'exfood_admin_add_order_item',
	   			post_id: post_id,
	   			food_id: food_id,
	   		};
	   		$.ajax({
	   			type: "post",
	   			url: exfood_ajax.ajaxurl,
	   			dataType: 'html',
	   			data: (param),
	   			success: function(data){
	   				if(data!=0){
		   				$('.exfd-order-items').removeClass('loading');
		   				$('.exfd-order-items table, .exfd-order-items .exfood-total').fadeOut(300, function() {
		                    $(this).remove();
		                });
		                $('.exfd-order-items').append(data);
		   				return true;
		   			}
	   			}	
	   		});
		});
		$('.exfd-order-items').on('click', '.exfood-close', function(event) {
			var $this_click = $(this);
			var it_remove  		= $this_click.data('remove');
			var post_id = $('#post_ID').val();
			$this_click.closest('tr').addClass('loading');
	   		var param = {
				action: 'adm_exfood_remove_cart_item',
				it_remove: it_remove,
				post_id: post_id,
			};
			$.ajax({
				type: "post",
				url: exfood_ajax.ajaxurl,
				dataType: 'json',
				data: (param),
				success: function(data){
					if(data != '0')
					{
						if(data.status == '0'){ 
						}
						else{
							$this_click.closest('tr').fadeOut(300, function() {
								$this_click.closest('tr').remove();
								$('.exfood-total > span').html(data.update_total);
							});
						}
					}else{ alert('error');}
				}
			});
			return false;
		});

		jQuery('.post-type-exfood_order').on('change paste keyup', 'input[name="food_qty"]',function() {
			var $this_click = $(this);
			$this_click.closest("tr").addClass('loading');
			var it_update  		= $this_click.data('update');
			var $value = $this_click.val();
			var post_id = $('#post_ID').val();
			var param = {
				action: 'adm_exfood_update_cart_item',
				it_update: it_update,
				qty: $value,
				post_id: post_id,
			};
			$.ajax({
				type: "post",
				url: exfood_ajax.ajaxurl,
				dataType: 'json',
				data: (param),
				success: function(data){
					if(data != '0')
					{
						if(data.status == '0'){
							alert(data.info_text);
							$this_click.closest("tr").find('.exfood-close').trigger('click');
							if (data.number_item < 1) {	}
						}
						else{
							$this_click.closest("tr").removeClass('loading');
							$this_click.closest("tr").find('.exfood-cart-price').html(data.update_price);
							$('.exfood-total > span').html(data.update_total);
						}
					}else{ alert('error');}
				}
			});
			return false;
		});
		$('.exfd-order-items').on('click', '.remove-order-meta', function(event) {
			$(this).closest(".exfood-container").remove();
			return false;
		});
		$('.exfd-order-items').on('click', '.save-order-meta', function(event) {
			
			var order_meta = new Array();
			$('input[name="exfodd-order-meta[]"]').each(function() {
			   order_meta.push($(this).val());
			});
			var $this_click = $(this);
			$this_click.closest("tr").addClass('loading');
			var it_update  		= $this_click.data('update');
			var post_id = $('#post_ID').val();
			
			var param = {
				action: 'adm_exfood_add_order_meta',
				metas: order_meta,
				it_update: it_update,
				post_id: post_id,
			};
			$.ajax({
				type: "post",
				url: exfood_ajax.ajaxurl,
				dataType: 'json',
				data: (param),
				success: function(data){
					if(data != '0')
					{
						if(data.status == '0'){
							
						}
						else{
							$this_click.closest("tr").removeClass('loading');
							$this_click.closest("tr").find('.exfood-container').remove();
							$this_click.closest("tr").find('.exfood-add-order-item-meta').before(data.html_add);
							$this_click.closest("tr").find('.exfood-cart-price').html(data.update_price);
							$('.exfood-total > span').html(data.update_total);
						}
					}else{ alert('error');}
				}
			});
			return false;
		});

		jQuery("body").on('click', ".exfood-add-order-item-meta .add-order-meta", function(e) {
			var $t_pl = jQuery(this).data('pl')
			jQuery(this).before( '<div class="exfood-container"><input type="text" placeholder="'+$t_pl+'" name="exfodd-order-meta[]" value="" /><span class="button remove-order-meta">×</span></div>' );
		});
		function ex_add_title($box){
			$box.find( '.cmb-group-title' ).each( function() {
				var $this = $( this );
				var txt = $this.next().find( '[id$="_name"]' ).val();
				var rowindex;
				if ( ! txt ) {
					txt = $box.find( '[data-grouptitle]' ).data( 'grouptitle' );
					if ( txt ) {
						rowindex = $this.parents( '[data-iterator]' ).data( 'iterator' );
						txt = txt.replace( '{#}', ( rowindex + 1 ) );
					}
				}
				if ( txt ) {
					$this.text( txt );
				}
			});
		}
		function ex_replace_title(evt){
			var $this = $( evt.target );
			var id = 'name';
			if ( evt.target.id.indexOf(id, evt.target.id.length - id.length) !== -1 ) {
				$this.parents( '.cmb-row.cmb-repeatable-grouping' ).find( '.cmb-group-title' ).text( $this.val() );
			}
		}
		jQuery('#exfood_addition_options,#exfood_custom_data').on( 'cmb2_add_row cmb2_shift_rows_complete', ex_add_title )
				.on( 'keyup', ex_replace_title );
		ex_add_title(jQuery('#exfood_addition_options,#exfood_custom_data'));

		jQuery('.cmb2-id-exorder-store input[name="exorder_store"]').on('change paste keyup',function(e) {
			e.preventDefault();
			var $this = $(this);
			var store_id = $this.val();
			var param = {
	   			action: 'exfood_admin_show_store',
	   			store_id: store_id,
	   		};
	   		$.ajax({
	   			type: "post",
	   			url: exfood_ajax.ajaxurl,
	   			dataType: 'json',
	   			data: (param),
	   			success: function(data){
	   				if(data!=0){
		   				$('.cmb2-id-exorder-store .cmb2-metabox-description').empty();
		   				$('.cmb2-id-exorder-store .cmb2-metabox-description').append(data.store_name);
		   			}
	   			}	
	   		});
		});

	});
}(jQuery));

(function(){
"use strict";
jQuery(document).ready(function($){
	if($('.cmb2-post-search-button').length || $('.cmb-type-post-search-text').length ){
		var SearchView = window.Backbone.View.extend({
			el         : '#find-posts',
			overlaySet : false,
			$overlay   : false,
			$idInput   : false,
			$checked   : false,

			events : {
				'keypress .find-box-search :input' : 'maybeStartSearch',
				'keyup #find-posts-input'  : 'escClose',
				'click #find-posts-submit' : 'selectPost',
				'click #find-posts-search' : 'send',
				'click #find-posts-close'  : 'close',
			},

			initialize: function() {
				this.$spinner  = this.$el.find( '.find-box-search .spinner' );
				this.$input    = this.$el.find( '#find-posts-input' );
				this.$response = this.$el.find( '#find-posts-response' );
				this.$overlay  = $( '.ui-find-overlay' );

				this.listenTo( this, 'open', this.open );
				this.listenTo( this, 'close', this.close );
			},

			escClose: function( evt ) {
				if ( evt.which && 27 === evt.which ) {
					this.close();
				}
			},

			close: function() {
				this.$overlay.hide();
				this.$el.hide();
			},

			open: function() {
				this.$response.html('');

				// WP, why you so dumb? (why isn't text in its own dom node?)
				this.$el.show().find( '#find-posts-head' ).html( this.findtxt + '<div id="find-posts-close"></div>' );

				this.$input.focus();

				if ( ! this.$overlay.length ) {
					$( 'body' ).append( '<div class="ui-find-overlay"></div>' );
					this.$overlay  = $( '.ui-find-overlay' );
				}

				this.$overlay.show();

				// Pull some results up by default
				this.send();

				return false;
			},

			maybeStartSearch: function( evt ) {
				if ( 13 == evt.which ) {
					this.send();
					return false;
				}
			},

			send: function() {

				var search = this;
				search.$spinner.addClass('is-active');

				$.ajax( ajaxurl, {
					type     : 'POST',
					dataType : 'json',
					data     : {
						ps               : search.$input.val(),
						action           : 'find_posts',
						cmb2_post_search : true,
						post_search_cpt  : search.posttype,
						_ajax_nonce      : $('#find-posts #_ajax_nonce').val()
					}
				}).always( function() {

					search.$spinner.removeClass('is-active');

				}).done( function( response ) {

					if ( ! response.success ) {
						search.$response.text( search.errortxt );
					}

					var data = response.data;

					if ( 'checkbox' === search.selecttype ) {
						data = data.replace( /type="radio"/gi, 'type="checkbox"' );
					}

					search.$response.html( data );

				}).fail( function() {
					search.$response.text( search.errortxt );
				});
			},

			selectPost: function( evt ) {
				evt.preventDefault();

				this.$checked = $( '#find-posts-response input[type="' + this.selecttype + '"]:checked' );

				var checked = this.$checked.map(function() { return this.value; }).get();

				if ( ! checked.length ) {
					this.close();
					return;
				}

				this.handleSelected( checked );
			},

			handleSelected: function( checked ) {
				checked = checked.join( ', ' );

				if ( 'add' === this.selectbehavior ) {
					var existing = this.$idInput.val();
					if ( existing ) {
						checked = existing + ', ' + checked;
					}
				}

				this.$idInput.val( checked ).trigger( 'change' );
				this.close();
			}

		});

		window.cmb2_post_search = new SearchView();

		window.cmb2_post_search.closeSearch = function() {
			window.cmb2_post_search.trigger( 'close' );
		};

		window.cmb2_post_search.openSearch = function( evt ) {
			var search = window.cmb2_post_search;

			search.$idInput = $( evt.currentTarget ).parents( '.cmb-type-post-search-text' ).find( '.cmb-td input[type="text"]' );
			// Setup our variables from the field data
			$.extend( search, search.$idInput.data( 'search' ) );

			search.trigger( 'open' );
		};

		window.cmb2_post_search.addSearchButtons = function() {
			var $this = $( this );
			var data = $this.data( 'search' );
			$this.after( '<div title="'+ data.findtxt +'" class="dashicons dashicons-search cmb2-post-search-button"></div>');
		};

		$( '.cmb-type-post-search-text .cmb-td input[type="text"]' ).each( window.cmb2_post_search.addSearchButtons );

		$( '.cmb2-wrap' ).on( 'click', '.cmb-type-post-search-text .cmb-td .dashicons-search', window.cmb2_post_search.openSearch );
		$( 'body' ).on( 'click', '.ui-find-overlay', window.cmb2_post_search.closeSearch );
	}

});
})(jQuery);