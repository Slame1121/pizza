
// Cart add remove functions
var cart = {

	removeProductFromCart: function () {
		var self = this;
		$('#cart-products-list').on('click', '.basket-log__item-close', function(e){
			e.stopPropagation();
			e.preventDefault();
			self.remove($(this).data('cart-id'));
		});
	},
	updateProductsInCart: function () {
		var self = this;
		$('#cart-products-list').on('click', '.basket-log__item-btn--right', function (e) {
			e.stopPropagation();
			e.preventDefault();
			var counter_input = $(this).parent().find('input');
			var cart_id = counter_input.data('cart-id');
			var count = parseInt(counter_input.val()) + 1;
			counter_input.val(count);
			self.update(cart_id, count);
		}).on('click', '.basket-log__item-btn--left', function (e) {
			e.stopPropagation();
			e.preventDefault();
			var counter_input = $(this).parent().find('input');
			var cart_id = counter_input.data('cart-id');
			var count = parseInt(counter_input.val()) - 1;
			if (count > 0) {
				counter_input.val(count);
				self.update(cart_id, count);
			}
		});

		$('#clear_basket').on('click', function (e) {
			e.stopPropagation();
			e.preventDefault();
			$('#cart-products-list').empty();
			self.removeAll();

		});
	}
	,
	init: function(){
		this.removeProductFromCart();

		this.updateProductsInCart();
	},

	'removeAll' : function(){
		var self = this;
		$.ajax({
			url: 'index.php?route=checkout/cart/removeAll',
			type: 'post',
			dataType: 'json',
			beforeSend: function() {
			},
			complete: function() {
			},
			success: function(json) {
				self.recalcBasketPrices();
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'add': function(product_id, quantity, options, attrs) {
		var self = this;
		$.ajax({
			url: 'index.php?route=checkout/cart/add',
			type: 'post',
			data: 'product_id=' + product_id + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1)+'&'+options+'&'+attrs,
			dataType: 'json',
			beforeSend: function() {

			},
			complete: function() {

			},
			success: function(json) {
				$('.alert-dismissible, .text-danger').remove();

				if (json['redirect']) {
					location = json['redirect'];
				}

				if (json['success']) {

					// Need to set timeout otherwise it wont update the total
					$('#cart-products-list').load('/index.php?route=common/cart/info', function() {
						$('html, body').animate({ scrollTop: 0 }, 'slow');
						var log = $('.basket-log');

						if(!log.hasClass('opened')){
							log.toggleClass('opened');
						}

						setTimeout(function () {
							$('.basket-log__summa').html( json['total']);
							self.recalcBasketPrices();
						}, 100);
					});






				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},

	'recalcBasketPrices' : function(){
		var inputs = $('#cart-products-list').find('input.product_counter_input');
		var total_summ = 0;
		var total_count = 0;
		$.each(inputs, function(key, input){
			var price = parseInt($(input).data('product-price'));
			var cart_id = $(input).data('cart-id');
			var count  = parseInt($(input).val());
			var total_price = (count * price);
			var real_price = (count * price);
			var discount_str = '';
			if($(input).data('discount') == 1){
				total_price = parseFloat(price*count);
				real_price = parseFloat(price/2 + price * (count - 1));
				discount_str= ' (-'+(price/2).toFixed(2).replace(/[.,]00$/, "")+' грн (50%))';
			}
			if($(input).data('b-day-discount') == 1){
				total_price = parseFloat(parseFloat(price * count).toFixed(2).replace(/[.,]00$/, ""));
				real_price = parseFloat((price - price * 0.15) * count);
				discount_str = ' (-'+parseFloat(price * 0.15 * count).toFixed(2).replace(/[.,]00$/, "")+' грн (15%))';
			}
			$('#basket-item-'+cart_id).find('.basket-log__item-price').html(total_price + ' грн' + discount_str);
			total_summ += real_price;
			total_count += count;
		});
		$('.basket-log__summa').html(total_summ + ' грн');
		$('.header-basket__summa').html(total_summ + ' грн');
		$('.header-basket__icon').attr('data-items',total_count)
	},
	'update': function(key, quantity) {
		var self= this;
		$.ajax({
			url: 'index.php?route=checkout/cart/edit',
			type: 'post',
			data: 'key=' + key + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			beforeSend: function() {
			},
			complete: function() {
			},
			success: function(json) {
				if('discount_cart_id' in json){
					if(parseInt(json['discount_cart_id']) == 0){
						$('.product_counter_input').data('discount', 0).removeAttr('data-discount');
					}else{
						var container = $('#basket-item-' +json['discount_cart_id']);
						var discount = container.find('input[data-discount]').length;
						if(!discount){
							$('.product_counter_input').data('discount', 0).removeAttr('data-discount');
						}
						container.find('input.product_counter_input').data('discount', 1);
					}

				}
				self.recalcBasketPrices();
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function(key) {
		var self = this;
		$.ajax({
			url: 'index.php?route=checkout/cart/remove',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',
			beforeSend: function() {
			},
			complete: function() {
			},
			success: function(json) {
				if('discount_cart_id' in json){
					if(parseInt(json['discount_cart_id']) == 0){
						$('.product_counter_input').data('discount', 0).removeAttr('data-discount');
					}else{
						var container = $('#basket-item-' +json['discount_cart_id']);
						var discount = container.find('input[data-discount]').length;
						if(!discount){
							$('.product_counter_input').data('discount', 0).removeAttr('data-discount');
						}
						container.find('input.product_counter_input').data('discount', 1);
					}

				}
				$('#basket-item-'+key).remove();
				$('.basket-log__summa').html(json['total'])
				self.recalcBasketPrices();
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
};
$('document').ready(function(){
	cart.init();
});