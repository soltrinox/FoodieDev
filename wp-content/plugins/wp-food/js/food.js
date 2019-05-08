;(function($){
	'use strict';
	function exfd_flytocart(imgtodrag){
		var cart = jQuery('.exfd-shopping-cart');
		if (cart.length == 0) {return;}
	    if (imgtodrag) {
	        var imgclone = imgtodrag.clone().offset({
	            top: imgtodrag.offset().top,
	            left: imgtodrag.offset().left
	        }).css({
	            'opacity': '0.5',
	                'position': 'absolute',
	                'height': '150px',
	                'width': '150px',
	                'z-index': '1001'
	        }).appendTo(jQuery('body'))
	            .animate({
	            'top': cart.offset().top + 10,
	                'left': cart.offset().left,
	                'width': 40,
	                'height': 40
	        }, 800);
	        imgclone.animate({
	            'width': 0,
	                'height': 0
	        }, function () {
	            jQuery(this).detach()
	        });
	    }
	}
	$(document).ready(function() {

		// Start Modal
		var ex_html_width;
		ex_html_width = $('html').width();
		$( window ).resize(function() {
			$('html').css("max-width","");

			ex_html_width = $('html').width();
			if ($(".ex_modal.exfd-modal-active").css("display") =='block') {
				$('html').css("max-width",ex_html_width);
			}
		});
		//woo fn
		function woo_add_pls_mn(){
			jQuery( '#food_modal .exfood-woocommerce .cart div.quantity:not(.buttons_added)' ).addClass( 'buttons_added' ).append( '<input type="button" value="+" id="add_ticket" class="plus" />' ).prepend( '<input type="button" value="-" id="minus_ticket" class="minus" />' );
			jQuery('.exfood-woocommerce .buttons_added').on('click', '#minus_ticket',function() {
				var value = parseInt(jQuery(this).closest(".quantity").find('.qty').val()) - 1;
				if(value>0){
					jQuery(this).closest(".quantity").find('.qty').val(value);
				}
			});
			jQuery('.exfood-woocommerce .buttons_added').on('click', '#add_ticket',function() {
				var value = parseInt(jQuery(this).closest(".quantity").find('.qty').val()) + 1;
				jQuery(this).closest(".quantity").find('.qty').val(value);
			});
		}
		// popup
	    $('.ex-fdlist.ex-food-plug .parent_grid .ctgrid, .ex-fdlist .ctlist').on("click","a", function(event){
	    	event.preventDefault();
	    	var id_crsc = $(this).closest(".ex-fdlist ").attr('id');
	    	var layout = $('#'+id_crsc).hasClass('table-layout') ? 'table' : '';
	    	if ($('#'+id_crsc).hasClass('ex-fdcarousel')) {
	    		layout = 'Carousel';
	    	}
	    	var $this_click;
	    	if (layout != 'table') {
	    		$this_click = $(this).closest(".item-grid");
	    	}else{
	    		$this_click = $(this).closest("tr");
	    	}
	    	if($this_click.hasClass('ex-loading')){ return;}
	    	$this_click.addClass('ex-loading');
	    	var id_food = $this_click.data('id_food');
	    	var ajax_url  		= $('#'+id_crsc+' input[name=ajax_url]').val();
	    	var param = {
				action: 'exfood_booking_info',
				id_food: id_food,
				id_crsc: id_crsc,
			};
			$.ajax({
				type: "post",
				url: ajax_url,
				dataType: 'html',
				data: (param),
				success: function(data){
					if(data != '0')
					{
						if(data == ''){ 
							$('.row.loadmore').html('error');
						}else{
							$("#food_modal").empty();
							$("#food_modal").append(data);
							// Variation Form
			                var form_variation = $("#food_modal .modal-content").find('.variations_form');
			                form_variation.each( function() {
			                    $( this ).wc_variation_form();
			                });
			                // woo_add_pls_mn();
			                form_variation.trigger( 'check_variations' );
			                form_variation.trigger( 'reset_image' );
			                if (typeof $.fn.init_addon_totals === 'function') {
			                	$( 'body' ).find( '.cart:not(.cart_group)' ).each( function() {
									$( this ).init_addon_totals();
								});
			                }
			                // remove loading
							$this_click.removeClass('ex-loading');
							
							$('html').css("max-width",ex_html_width);
					        $("html").fadeIn("slow", function() {
							    $(this).addClass('exfd-hidden-scroll');
							});
							$("#food_modal").css("display", "block");
							$("#food_modal").addClass('exfd-modal-active');
							var rtl_mode = $("#food_modal .exfd-modal-carousel").attr('rtl_mode');
							$("#food_modal .exfd-modal-carousel").EX_ex_s_lick({
								dots: true,
								slidesToShow: 1,
								infinite: true,
								speed: 500,
								fade: true,
								cssEase: 'linear',
								arrows: false,
								rtl:rtl_mode =='yes' ? true : false,

							});
						}
						
					}else{$('.row.loadmore').html('error');}
				}
			});
			return false;
	    });
	    // cart content
	    $('.exfd-shopping-cart').on("click", function(event){
			event.preventDefault();
			$(".exfd-cart-content").addClass('excart-active');
			$(".exfd-overlay").addClass('exfd-overlay-active');
			return false;
		});	
		$('.exfd-cart-content .exfd-close-cart, .exfd-overlay').on("click",function(event){
			$(".exfd-cart-content").removeClass('excart-active');
			$(".exfd-overlay").removeClass('exfd-overlay-active');
			return false;
		});
		$('.ex-fdlist.ex-food-plug').on("click", ".exfd-choice", function(event){
			if($(this).prev('.ex-hidden').find('form').length){
				$(this).addClass('loading');
				$(this).prev('.ex-hidden').find('form button').trigger('click');
			}else{
				$(this).prev('.ex-hidden').find('a').trigger('click');
			}
			return false;
		});
		$("body").on("submit", ".exform", function(e){
			e.preventDefault();
			var $validate = true;
			$('.ex-required-message').fadeOut();
			jQuery('.exrow-group.ex-required').each(function(){
				var $this_sl = $(this);
				if($this_sl.hasClass('ex-radio') || $this_sl.hasClass('ex-checkbox')){
					if(!$this_sl.find('.ex-options').is(":checked")){
						$this_sl.find('.ex-required-message').fadeIn();
						$validate = false;
					}
				}else{
					if($this_sl.find('.ex-options').val() == ''){
						$this_sl.find('.ex-required-message').fadeIn();
						$validate = false;
					}
				}
				
			});
			if($validate != true){
				return;
			}
			var $this_click = $(this);
			$(this).addClass('loading');
			var form = $(e.target);
			//console.log(form.serialize());
			var param = {
				action: 'exfood_add_cart_item',
				data:form.serialize()
			};
			var ajax_url  		= $('.ex-fdlist input[name=ajax_url]').val();
			jQuery.ajax({
				type: "post",
				url: ajax_url,
				dataType: 'json',
				data: (param),
	            success: function(data) {
	            	if(data!=0){
	            		if(data.cart_content!=''){
	            			$('.exfd-cart-content .exfd-cart-buildin').fadeOut(300, function() {
								$(this).remove();
							});
							$('.exfd-cart-content .exfood-warning').fadeOut(300, function() {
								$(this).remove();
							});
							$('.exfd-cart-content').append(data.cart_content);
							setTimeout(function(){ 
								$('.exfd-cart-count').html($(".exfd-cart-buildin > ul > li").length);
							}, 500);
							$this_click.find('.ex-order').addClass('exhidden');
							$this_click.find('.ex-added').removeClass('exhidden');
	            		}
		            	$this_click.removeClass('loading');
		            	if ($this_click.closest('.ex-hidden').next().hasClass('exfd-choice')) {
		            		$this_click.closest('.ex-hidden').next().removeClass('loading');
		            	}
		                // console.log(data);
		                var imgtodrag;
		                var id_parent =$this_click.closest(".ex-fdlist ").attr('id');
		                var layout = $('#'+id_parent).hasClass('table-layout') ? 'table' : '';
		                imgtodrag = $this_click.closest("#food_modal").find("img").eq(0);
		                if (imgtodrag.length == 0) {
		                	if (layout!='table') {
			                	imgtodrag = $this_click.closest(".item-grid").find("img").eq(0);
			                	if (imgtodrag.length == 0) {
			                		imgtodrag = $this_click.closest(".item-grid").find(".ex-fly-cart").eq(0);
			                	}
			                }else{
			                	imgtodrag = $this_click.closest("tr").find("img").eq(0);
			                }
		                }
		                exfd_flytocart(imgtodrag);
		            }
	            }
	        });
	        return false;
		});
	    $(".ex-food-plug #food_modal").on("click", ".ex_close",function(event){
	    	event.preventDefault();
	    	var $this = $(this);
	        $("#food_modal").css("display", "none");
			$('html').removeClass('exfd-hidden-scroll');
			$("#food_modal").removeClass('exfd-modal-active');
			$('html').css("max-width","");
	    });
		$('.ex-food-plug .ex_modal').on('click', function (event) {
			if (event.target.className == 'ex_modal exfd-modal-active') {
				event.preventDefault();
				$(this).css("display", "none");
				$('html').removeClass('exfd-hidden-scroll');
				$(this).removeClass('exfd-modal-active');
				$('html').css("max-width","");
			}
		});
		// End Modal
		// Js Quality
		$(".ex-food-plug .fd_quality_parent .fd_quality").on("click",".fd_quality_btn" ,function(event){
	    	event.preventDefault();
	        var $this = $(this);
	        var type= $this.attr('type');
	        var quality = parseFloat($this.parent().find("input").val());
	        if (type == "plus") {
	        	quality += 1;
	        	$this.parent().find("input").val(quality);
	        }else{
	        	if (quality==0) {
	        		return;
	        	}
	        	quality -= 1;
	        	$this.parent().find("input").val(quality);
	        }
	    });
		// End quality

		// Js popup location
		var $popup_loc = $(".ex-popup-location");
		$popup_loc.addClass("ex-popup-active");
		// End popup location

		// Js Category
		$('.ex-food-plug .ex-menu-list .ex-menu-item').on('click',function(event) {
			event.preventDefault();
        	var $this = $(this);
        	var $parent = $this.closest(".ex-fdlist");
        	if (!$parent.hasClass("category_left")) {
        		$this.parent().find(".ex-menu-item").removeClass("ex-menu-item-active");
	        	$this.addClass("ex-menu-item-active");
        	}else{
        		$this.parent().find(".ex-menu-item").removeClass("ex-active-left");
	        	$this.addClass("ex-active-left");
        	}

			var $this_click = $(this);
			var id_crsc = $this_click.closest(".ex-fdlist").attr('id');
			var cat = $this.attr("data-value");
			var key_word = $('#'+id_crsc+' input[name=s]').val();
			var mode = 'search';
			exfd_ajax_search($this_click,'',key_word,cat,mode);
			return false;
		});

		$('.ex-fdlist.ex-food-plug .ex-menu-select select[name=exfood_cat]').on('change',function(event) {
			event.preventDefault();
			var $this_click = $(this);
			var id_crsc = $this_click.closest(".ex-fdlist").attr('id');
			var cat = $('#'+id_crsc+' select[name=exfood_cat]').val();
			var key_word = $('#'+id_crsc+' input[name=s]').val();
			var mode = 'search';
			exfd_ajax_search($this_click,'',key_word,cat,mode);
			return false;
		});
		//

		// Js SEARCH
		function exfd_ajax_search($this_click,$char_ft, $key_word,$cat,mode){
			var id_crsc = $this_click.closest(".ex-fdlist").attr('id');
			var layout = $('#'+id_crsc).hasClass('table-layout') ? 'table' : '';
			if($('#'+id_crsc).hasClass('loading')){ return;}
			$('#'+id_crsc).addClass("loading");
			if($('#'+id_crsc).hasClass('list-layout')){ layout = 'list';}
			var param_query  		= $('#'+id_crsc+' input[name=param_query]').val();
			var ajax_url  		= $('#'+id_crsc+' input[name=ajax_url]').val();
			var param_shortcode  		= $('#'+id_crsc+' input[name=param_shortcode]').val();
			var param = {
				action: 'exfood_category',
				param_query: param_query,
				id_crsc: id_crsc,
				param_shortcode: param_shortcode,
				layout: layout,
				char: $char_ft,
				key_word: $key_word,
				cat: $cat,
			};
			$.ajax({
				type: "post",
				url: ajax_url,
				dataType: 'json',
				data: (param),
				success: function(data){
					if(data != '0')
					{
						if($('#'+id_crsc+' .ex-loadmore').length){
							var $loadmore=1;
							if(data.page_navi =='off'){
								$('#'+id_crsc+' .ex-loadmore .loadmore-exfood').remove();
							}else{
								$('#'+id_crsc+' .ex-loadmore').remove();	
							}
							
						};
						$('#'+id_crsc+' input[name=num_page_uu]').val('1');
						$('#'+id_crsc+' input[name=current_page]').val('1');
						var $showin='';
						if(layout=='table'){
							$showin = $('#'+id_crsc+' table tbody');
						}else if(layout=='list'){
							$showin = $('#'+id_crsc+' .ctlist');
						}else{
							$showin = $('#'+id_crsc+' .ctgrid');
						}
						$($showin).fadeOut({
							duration:0,
							complete:function(){
								$( this ).empty();
							}
						});
						if(data.page_navi !='' && data.page_navi !='off'){
							if ($loadmore ==1) {
								$('#'+id_crsc).append(data.page_navi);
							}
							else{
								$('#'+id_crsc+' .exfd-pagination').fadeOut({
									duration:0,
									complete:function(){
										$( this ).remove();
									}
								});
								$('#'+id_crsc+' .exfd-pagination-parent').append(data.page_navi);
							}
						}else if(data.page_navi=='off'){
								$('#'+id_crsc+' .exfd-pagination .page-navi').fadeOut({
									duration:0,
									complete:function(){
										$( this ).remove();
									}
								});
						}
						$('#'+id_crsc).removeClass("loading");
						$showin.append(data.html_content).fadeIn();
						if(data.html_modal!=''){
							$('#'+id_crsc+' .ex-hidden .exp-mdcontaner').fadeOut({
								duration:0,
								complete:function(){
									$( this ).empty();
								}
							});
							$('#'+id_crsc+' .ex-hidden .exp-mdcontaner').append(data.html_modal).fadeIn();
						}
						exfd_loadmore();
					}else{$('#'+id_crsc+' .loadmore-exfood').html('error');}
				}
			});
			
		};
		// END SEARCH

		// Load more
		function exfd_loadmore(){
			$('.ex-food-plug .loadmore-exfood').on('click',function() {
				if($(this).hasClass('disable-click')){
					return;
				}
				var $this_click = $(this);
				var id_crsc  = $this_click.closest(".ex-fdlist").attr('id');
				exfd_ajax_load_page('loadmore' ,$this_click,id_crsc,'');
			});
		}
		exfd_loadmore();
		// Page number
		$('.ex-fdlist.ex-food-plug .exfd-pagination-parent').on('click','.page-numbers',function(event) {
			event.preventDefault();
			var $this_click = $(this);
			var id_crsc  		= $this_click.closest(".ex-fdlist").attr('id');
			$('#'+id_crsc+' .page-numbers').removeClass('current');
			$($this_click).addClass('current');
			var $page_link = $this_click.text();
			if($page_link*1 > 1){
				$('#'+id_crsc+' .prev-ajax').removeClass('disable-click');
			}
			$('#'+id_crsc+' .next-ajax').removeClass('disable-click');
			exfd_ajax_load_page('page_link',$this_click,id_crsc,$page_link);
		});
		$('.ex-fdlist.ex-food-plug .exfd-pagination-parent').on('click','.next-ajax',function(event) {
			event.preventDefault();
			var $this_click = $(this);
			var id_crsc = $this_click.closest(".ex-fdlist").attr('id');
			var $current =  $('#'+id_crsc+' .current');
			var current_page =  $current.text();
			$('#'+id_crsc+' .prev-ajax').removeClass('disable-click');

			$current.removeClass('current');
			$current.next().addClass('current');
			$page_link = current_page*1+1;
			exfd_ajax_load_page($style ='page_link',$this_click,id_crsc,$page_link);
			$this_click.removeClass('disable-click');
		});
		$('.ex-fdlist.ex-food-plug .exfd-pagination-parent').on('click','.prev-ajax',function(event) {
			event.preventDefault();
			var $this_click = $(this);
			var id_crsc = $this_click.closest(".ex-fdlist").attr('id');
			var $current =  $('#'+id_crsc+' .page-navi .current');
			var current_page =  parseInt($current.text());
			$('#'+id_crsc+' .next-ajax').removeClass('disable-click');
			if (current_page == 1) {
				$('#'+id_crsc+' .prev-ajax').addClass('disable-click');
				return false;
			}
			$current.removeClass('current');
			$current.prev().addClass('current');
			$page_link = current_page-1;
			exfd_ajax_load_page($style ='page_link',$this_click,id_crsc,$page_link);
			if($page_link*1 > 1){
				$('#'+id_crsc+' .prev-ajax').removeClass('disable-click');
			}
		});
		function exfd_ajax_load_page($style,$this_click,id_crsc,$page_link){
			if($style !='loadmore'){
				$('#'+id_crsc+' .page-numbers').removeClass('disable-click');
			}
			$this_click.addClass('disable-click');
			var n_page = $('#'+id_crsc+' input[name=num_page_uu]').val();
			if($style=='loadmore'){
				$('#'+id_crsc+' .loadmore-exfood').addClass("loading");
			}else{
				$('#'+id_crsc).addClass("loading");
			}
			var layout = $('#'+id_crsc).hasClass('table-layout') ? 'table' : '';
			if($('#'+id_crsc).hasClass('list-layout')){ layout = 'list';}
			var param_query  		= $('#'+id_crsc+' input[name=param_query]').val();
			var param_ids  		= $('#'+id_crsc+' input[name=param_ids]').val();
			var page  		= $('#'+id_crsc+' input[name=current_page]').val();
			var num_page  		= $('#'+id_crsc+' input[name=num_page]').val();
			var ajax_url  		= $('#'+id_crsc+' input[name=ajax_url]').val();
			var param_shortcode  		= $('#'+id_crsc+' input[name=param_shortcode]').val();
			var char_ft = $('#'+id_crsc+' .etp-alphab a').length ? $('#'+id_crsc+' .etp-alphab a.current').data('value') : '';
				var param = {
					action: 'exfood_loadmore',
					param_query: param_query,
					param_ids: param_ids,
					id_crsc: id_crsc,
					page: $page_link!='' ? $page_link : page*1+1,
					param_shortcode: param_shortcode,
					layout: layout,
					char: char_ft,
				};
				$.ajax({
					type: "post",
					url: ajax_url,
					dataType: 'json',
					data: (param),
					success: function(data){
						if(data != '0')
						{
							if($style=='loadmore'){
								n_page = n_page*1+1;
								$('#'+id_crsc+' input[name=num_page_uu]').val(n_page)
								if(data.html_content == ''){ 
									$('#'+id_crsc+' .loadmore-exfood').remove();
								}else{
									$('#'+id_crsc+' input[name=current_page]').val(page*1+1);
									if(layout=='table'){
										var $g_container = $('#'+id_crsc+' table tbody');
										$g_container.append(data.html_content);
									}else if(layout=='list'){
										var $g_container = $('#'+id_crsc+' .ctlist');
										$g_container.append(data.html_content);
									}else{
										var $g_container = $('#'+id_crsc+' .ctgrid');
										$g_container.append(data.html_content);
										setTimeout(function(){ 
											$('#'+id_crsc+' .item-grid').addClass("active");
										}, 200);
									}
									$('#'+id_crsc+' .loadmore-exfood').removeClass("loading");
									$this_click.removeClass('disable-click');
								}
								if(n_page == num_page){
									$('#'+id_crsc+' .loadmore-exfood').remove();
								}
							}else{
								var $showin ='';
								if(layout=='table'){
									$showin = $('#'+id_crsc+' table tbody');
								}else if(layout=='list'){
									$showin = $('#'+id_crsc+' .ctlist');
								}else{
									$showin = $('#'+id_crsc+' .ctgrid');
								}
								$($showin).fadeOut({
									duration:0,
									complete:function(){
										$( this ).empty();
									}
								});
								$('#'+id_crsc).removeClass("loading");
								$showin.append(data.html_content).fadeIn();

							}
							if(data.html_modal!=''){
								
								$('#'+id_crsc+' .ex-hidden .exp-mdcontaner').append(data.html_modal).fadeIn();
							}
							if($('#'+id_crsc).hasClass('extp-masonry') && !$('#'+id_crsc).hasClass('column-1')){
								if (typeof imagesLoaded === "function"){
									$('#'+id_crsc+'.extp-masonry .ctgrid').imagesLoaded( function() {
										$('#'+id_crsc+'.extp-masonry .ctgrid').masonry('reloadItems');
										$('#'+id_crsc+'.extp-masonry .ctgrid').masonry({
											isInitLayout : false,
											horizontalOrder: true,
											itemSelector: '.item-grid'
										});
									});
								}
							}
						}else{$('#'+id_crsc+' .loadmore-exfood').html('error');}
					}
				});
			return false;	
		}
		// end paging
		$('.exfd-cart-content').on('click', '.exfood-close', function(event) {
			event.preventDefault();
			var $this_click = $(this);
			var ajax_url  		= $('.exfd-cart-buildin input[name=ajax_url]').val();
			var it_remove  		= $this_click.data('remove');
			$this_click.closest('li').addClass('loading');
			var param = {
				action: 'exfood_remove_cart_item',
				it_remove: it_remove,
			};
			$.ajax({
				type: "post",
				url: ajax_url,
				dataType: 'json',
				data: (param),
				success: function(data){
					if(data != '0')
					{
						if(data.message != ''){
							$('.exfd-cart-content').append(data.message);
							$('.exfd-cart-content .exfd-cart-buildin').fadeOut(300, function() {
								$(this).remove();
							});
						}
						if(data.status == '0'){ 
						}
						else{
							$this_click.closest('li').fadeOut(300, function() {
								$this_click.closest('li').remove();
								$('.exfd-cart-count').html($(".exfd-cart-buildin > ul > li").length);
								$('.exfood-total > span').html(data.update_total);
							});
						}
					}else{ alert('error');}
				}
			});
			return false;	
		});
		// 
		jQuery('#food_modal').on('click', '.minus_food',function() {
			var value = parseInt(jQuery(this).closest(".exfood-quantity").find('.food_qty').val()) - 1;
			if(value>0){
				jQuery(this).closest(".exfood-quantity").find('.food_qty').val(value);
			}
		});
		jQuery('#food_modal').on('click', '.plus_food',function() {
			var value = parseInt(jQuery(this).closest(".exfood-quantity").find('.food_qty').val()) + 1;
			jQuery(this).closest(".exfood-quantity").find('.food_qty').val(value);
		});
		// cart shortcode update qty
		function exfood_update_cart($this_click, $value){
			$this_click.closest("li").addClass('loading');
			var ajax_url  		= $('.exfd-cart-buildin input[name=ajax_url]').val();
			var it_update  		= $this_click.data('update');
			var param = {
				action: 'exfood_update_cart_item',
				it_update: it_update,
				qty: $value,
			};
			$.ajax({
				type: "post",
				url: ajax_url,
				dataType: 'json',
				data: (param),
				success: function(data){
					if(data != '0')
					{
						if(data.status == '0'){
							alert(data.info_text);
							// $this_click.closest('li').remove();
							$this_click.closest("li").find('.exfood-close').trigger('click');
							if (data.number_item < 1) {
								
							}
						}
						else{
							$this_click.closest("li").removeClass('loading');
							$this_click.closest(".exfood-quantity").find('.food_qty').val($value);
							$this_click.closest("li").find('.exfood-cart-price').html(data.update_price);
							$('.exfood-total > span').html(data.update_total);
						}
					}else{ alert('error');}
				}
			});
			return false;
		}
		jQuery('.exfood-cart-shortcode').on('click', '.minus_food',function() {
			var $value = parseInt(jQuery(this).closest(".exfood-quantity").find('.food_qty').val()) - 1;
			if($value>0){
				var $this_click = jQuery(this);	
				exfood_update_cart($this_click, $value);

			}else{
				jQuery(this).closest("li").find('.exfood-close').trigger('click');
			}
		});
		jQuery('.exfood-cart-shortcode').on('click', '.plus_food',function() {
			var $value = parseInt(jQuery(this).closest(".exfood-quantity").find('.food_qty').val()) + 1;
			var $this_click = jQuery(this);	
			exfood_update_cart($this_click, $value);
		});
		// Carousel
		function exfd_carousel(id_clas,infinite,start_on,rtl_mode,slidesshow,slidesscroll,auto_play,auto_speed){
		  jQuery(id_clas).EX_ex_s_lick({
			infinite: infinite,
			initialSlide:start_on,
			rtl: rtl_mode =='yes' ? true : false,
			prevArrow:'<button type="button" class="ex_s_lick-prev"></button>',
			nextArrow:'<button type="button" class="ex_s_lick-next"></button>',	
			slidesToShow: slidesshow,
			slidesToScroll: slidesscroll,
			dots: true,
			autoplay: auto_play==1 ? true : false,
			autoplaySpeed: auto_speed!='' ? auto_speed : 3000,
			arrows: true,
			centerMode:  false,
			focusOnSelect: false,
			ariableWidth: true,
			adaptiveHeight: true,
			responsive: [
			  {
				breakpoint: 1024,
				settings: {
				  slidesToShow: slidesshow,
				  slidesToScroll: slidesscroll,
				}
			  },
			  {
				breakpoint: 768,
				settings: {
				  slidesToShow: 2,
				  slidesToScroll: 1
				}
			  },
			  {
				breakpoint: 480,
				settings: {
				  slidesToShow: 1,
				  slidesToScroll: 1
				}
			  }
			]
			  
		  });
		}
		jQuery('.ex-fdcarousel').each(function(){
			var $this = jQuery(this);
			var id =  $this.attr('id');
			var slidesshow =  $this.data('slidesshow');
			var slidesscroll =  $this.data('slidesscroll');
			if(slidesshow==''){ slidesshow = 3;}
			if (slidesscroll==''){ slidesscroll = slidesshow;}
			var startit =  $this.data('startit') > 0 ? $this.data('startit') : 1;
			var auto_play = $this.data('autoplay');
			var auto_speed = $this.data('speed');
			var rtl_mode = $this.data('rtl');
			var start_on =  $this.data('start_on') > 0 ? $this.data('start_on') : 0;
			if($this.data('infinite')=='0'){
			  var infinite = 0;
			}else{
			  var infinite =  $this.data('infinite') == 'yes' || $this.data('infinite') == '1' ? true : false;
			}
			exfd_carousel('#'+id+' .ctgrid',infinite,start_on,rtl_mode,slidesshow,slidesscroll,auto_play,auto_speed);
		});
		// jQuery(window).load(function(e) {
		// 	jQuery('.ex-fdcarousel.ld-screen').each(function(){
	    //         jQuery(this).addClass('at-childdiv');
	    //     });
        // });
        setTimeout(function() {
            jQuery('.ex-fdcarousel.ld-screen').each(function(){
	            jQuery(this).addClass('at-childdiv');
	        });
        }, 7000);
		// End Carousel
		$('.ex-loc-select').on('change', function () {
			var url = $(this).val(); // get selected value
			if (url) { // require a URL
			  window.location = url; // redirect
			}
			return false;
		});
		// checkout process
		$("body").on("submit", ".exform-checkout", function(e){
			if($(this).hasClass('loading')){return;}
			e.preventDefault();
			if (typeof grecaptcha !== 'undefined') {
				var cc_response = grecaptcha.getResponse();
				if (!cc_response) {
					alert($('.excheckout-submit .captcha_mes').val()); 
					return; 
				}
			}else{
				var cc_response ='';
			}
			var $this_click = $(this);
			$(this).addClass('loading');
			var form = $(e.target);
			//console.log(form.serialize());
			var param = {
			action: 'exfood_user_checkout',
			recaptcha: cc_response,
			data:form.serialize()
			};
			var ajax_url      = $('.exfood-buildin-checkout input[name=ajax_url]').val();
			jQuery.ajax({
			type: "post",
			url: ajax_url,
			dataType: 'json',
			data: (param),
			    success: function(data) {
			      if(data!=0){
			        $this_click.removeClass('loading');
			        if(data.status=='2'){
			          $('.exfood-validate-warning').remove();
			          $(data.html_content).insertAfter('.exfood-mulit-steps');
			          if (typeof grecaptcha !== 'undefined') { grecaptcha.reset();}
			        }else{
			          $('.exform-checkout').fadeOut(300, function() {
			            $(this).remove();
			          });
			          $('.exfood-checkout-shortcode .exfood-buildin-checkout').append(data.html_content);
			        //}else{
			        }
			        var windowHeight = $(window).height();
			        $('html,body').animate({
			          scrollTop: $(".exfood-mulit-steps").offset().top - windowHeight * .2},
			          'slow');
			      }
			    }
			});
			return false;
		});

		// js check order delivery or pick
		$('.exfood-buildin-checkout input[type=radio][name=_type]').change(function () {
			if ($(this).val() == 'order-pick') {
                $('.exfd-hide-order').addClass('exhidden');
            }else{
            	$('.exfd-hide-order').removeClass('exhidden');
            }
		});
		// js change location
		$('select.exfd-choice-locate').on('change', function() {
			var locate = $(this).val();
			// var $parent = $(this).closest(".exfood-buildin-checkout");
			var ajax_url = $('.exfood-buildin-checkout input[name=ajax_url]').val();
			var locate_param 		= locate;
			var param = {
				action: 'exfood_loadstore',
				locate_param: locate_param,
			};
			$('form.exform-checkout').addClass('loading');
			$.ajax({
				type: "post",
				url: ajax_url,
				dataType: 'json',
				data: (param),
				success: function(data){
					if(data != '0')
					{
						$('form.exform-checkout').removeClass('loading');
						$('.exfd-choice-store').empty();
						if (data.html_content == '0') {
							return false;
						}
						$('.exfd-choice-store').append(data.html_content);
						return false;
					}else{
					}
				}
			});
		});

    });
    // sort table
}(jQuery));