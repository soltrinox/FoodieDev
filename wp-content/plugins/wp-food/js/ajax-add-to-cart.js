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
	$(document).on('click', '.exfood-woocommerce .single_add_to_cart_button', function (e) {
		var $button = $(this);
		var $form = $button.closest('form.cart');
		var product_id = $form.find('input[name=add-to-cart]').val() || $button.val();
		if (!product_id){ return;}
		if ($button.is('.disabled')){ return;}
		e.preventDefault();
		var data = {
			action: 'exfood_add_to_cart',
			'add-to-cart': product_id,
		};
		$form.serializeArray().forEach(function (element) {
			data[element.name] = element.value;
		});
		$(document.body).trigger('adding_to_cart', [$button, data]);
		$.ajax({
			type: 'post',
			url: wc_add_to_cart_params.ajax_url,
			data: data,
			beforeSend: function (response) {
				$button.removeClass('added').addClass('loading');
			},
			complete: function (response) {
				$button.addClass('added').removeClass('loading');
			},
			success: function (response) {
				if (response.error & response.product_url) {
					window.location = response.product_url;
					return;
				} else {
					$(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
					$('.woocommerce-notices-wrapper').empty().append(response.notices);
					if($('.ex-fdlist.ex-food-plug .exfd-choice').length){
						$('.ex-fdlist.ex-food-plug .exfd-choice').removeClass('loading');
					}
					
	                var imgtodrag;
	                var id_parent =$button.closest(".ex-fdlist ").attr('id');
	                var layout = $('#'+id_parent).hasClass('table-layout') ? 'table' : '';
	                imgtodrag = $button.closest("#food_modal").find("img").eq(0);
	                if (imgtodrag.length == 0) {
	                	if (layout!='table') {
		                	imgtodrag = $button.closest(".item-grid").find("img").eq(0);
		                	if (imgtodrag.length == 0) {
		                		imgtodrag = $button.closest(".item-grid").find(".ex-fly-cart").eq(0);
		                	}
		                }else{
		                	imgtodrag = $button.closest("tr").find("img").eq(0);
		                }
	                }
	                exfd_flytocart(imgtodrag);

				}
			},
		});
		return false;
	});
    
}(jQuery));