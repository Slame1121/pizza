var Checkout = {
	cofirmCheckout: function () {
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

			var box = $(this).closest('.basket-log__box');

			box.next()
				.addClass('active')
				.siblings()
				.removeClass('active');
		});

		$('.basket-log__top-btn--left').click(function(e) {
			e.preventDefault();

			var box = $(this).closest('.basket-log__box');

			box.prev()
				.addClass('active')
				.siblings()
				.removeClass('active');
		});
	},

	getOrder: function(){
		if($('.product_counter_input').length > 0){
			$.ajax({
				url: 'index.php?route=checkout/checkout',
				dataType: 'html',
				success: function(html) {
					$('.basket-log__box').removeClass('active');
					$('#checkout-checkout').empty().addClass('active').append(html);
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}

	},
	init: function(){
		this.bindCheckoutButtons();
	}
};

$('document').ready(function(){
		Checkout.init();
});