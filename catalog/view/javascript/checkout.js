var Checkout = {
	cofirmCheckout: function () {

		var error = false;
		$('#checkout-checkout').find('.error').removeClass('error');
		var method = $('input[name=shipping_method]:checked').val();

		switch(method){
			case 'citylink.citylink':
				var adress =  $('#cart_adress_button').attr('data-type');
				if(typeof adress == 'undefined'){
					adress = 'new';
				}
				switch(adress){
					case 'new':
						var inputs = $('#cart-adress-new').find('input[required]');
						$.each(inputs, function(key, val){
							if($(val).val() == ''){
								$(val).parent().find('span').addClass('error');
								$(val).addClass('error');
								error = true;
							}
						});
						break;
				}
				break;
		}
		var email = $('#checkout-checkout').find('input[name=email]');
		var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		if(!regex.test(email.val())){
			email.parent().find('span').addClass('error');
			email.addClass('error')
			error = true;
		}
		if(!error){
			switch(method) {
				case 'citylink.citylink':
					break;
				case 'pickup.pickup':
					$('#cart-adress-new').remove();
					break;
			}
			$('#shipping_method_input').val(method);
			var datastring = $('#checkout-form').serialize();
			$.ajax({
				type: "POST",
				url: "/index.php?route=checkout/checkout/save",
				data: datastring,
				dataType: "json",
				success: function(data) {
					if('success' in data){
						$.ajax({
							type: "POST",
							url: "/index.php?route=checkout/confirm",
							dataType: "html",
							success: function(html) {
								$('.basket-log__box').removeClass('active');
								$('#checkout-confirm').empty().addClass('active').append(html);
								$('.basket-log__top-btn--left').attr('data-type', 'confirm');
							},
							error: function() {
								alert('error handing here');
							}
						});
					}
				},
				error: function() {
					alert('error handing here');
				}
			});
		}else{
			$('html,body').animate({
					scrollTop: $("#checkout-checkout").offset().top},
				'slow');
		}

	},
	bindCheckoutButtons: function () {
		var self = this;
		$('body').on('click','.basket-log__btn', function(e) {
			e.preventDefault();
			var type = $(this).attr('data-type');
			switch(type){
				case 'cart':
					self.getOrder();
					break;
				case 'checkout':
					self.cofirmCheckout();
					break;
			}


		}).on('click','.basket-log__top-btn--left',function(e) {
			e.preventDefault();

			var type = $(this).attr('data-type');
			switch(type){
				case 'checkout':
					$('.basket-log__box').removeClass('active');
					$('#checkout-cart').addClass('active');
					break;
				case 'confirm':
					self.getOrder();
					break;
			}
		});
	},

	getOrder: function(){
		if($('.product_counter_input').length > 0){
			$.ajax({
				url: '/index.php?route=checkout/checkout',
				dataType: 'html',
				success: function(html) {
					$('.basket-log__box').removeClass('active');
					$('#checkout-checkout').empty().addClass('active').append(html);
					$('.basket-log__top-btn--left').attr('data-type', 'checkout');
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}

	},
	bindAddAddress: function () {
		$('body').on('click', '#cart_adress_button', function(e){
			e.preventDefault();
			e.stopPropagation();

			var type = $(this).attr('data-type');
			if(type == 'saved'){
				$('#cart-adress-new').removeClass('hidden');
				$('#cart-adress-saved').addClass('hidden');
				$(this).attr('data-type', 'new');
				$(this).html('Выбрать из сохранённых');
			}else{
				$('#cart-adress-new').addClass('hidden');
				$('#cart-adress-saved').removeClass('hidden');
				$(this).attr('data-type', 'saved');
				$(this).html('+ Добавить адресс');
			}



		})
	},
	changeShippingMethod: function () {
		$('#checkout-checkout').on('click', '.basket-log__tabs-box', function(){
			var method = $(this).find('input').val();
			switch (method){
				case 'citylink.citylink':
					$('#cart-pickup').addClass('hidden');
					if($('#cart_adress_button').length < 1 || $('#cart_adress_button').attr('data-type') == 'new'){
						$('#cart-adress-new').removeClass('hidden');
					}else{
						$('#cart-adress-saved').removeClass('hidden');
					}
					$('#change_adress_button_container').removeClass('hidden');
					break;
				case 'pickup.pickup':
					$('#cart-adress-new').addClass('hidden');
					$('#cart-pickup').removeClass('hidden');
					$('#change_adress_button_container').addClass('hidden');
					$('#cart-adress-saved').addClass('hidden');
					break;
			}

		})
	},
	successOrder: function(){

		$.ajax({
			url: '/index.php?route=checkout/success',
			dataType: 'html',
			success: function(html) {
				$('.basket-log__box').removeClass('active');
				$('#checkout-success').empty().addClass('active').append(html);
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
		$('#cart-products-list').empty();
		cart.recalcBasketPrices();
	},
	init: function(){
		this.bindCheckoutButtons();

		this.bindAddAddress();

		this.changeShippingMethod();
	}
};

$('document').ready(function(){
		Checkout.init();
});