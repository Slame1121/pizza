var Account = {
	removeAdress: function () {
		$('.profile-form__box-current-adresses-adress-row_remove').on('click', function(){
			var address_id = $(this).data('address-id');
			var self = $(this);
			$.ajax({
				url: 'index.php?route=account/address/delete',
				type: 'post',
				data: {address_id: address_id},
				dataType: 'html',
				beforeSend: function() {
				},
				complete: function() {
				},
				success: function(html) {
					self.parent().remove();
				}
			});
		})
	},
	noNeedToAddNewAdress: function () {
		$('.profile-form__btn').on('click', function(){
			if(!$('.profile-form__drop').hasClass('opened')){
				$('.profile-form__drop').remove();
			}
		})
	},
	telephoneMask: function () {
		$('input[name=telephone]').mask('+38 000 000 00 00');
	},
	reOrder: function () {
		$('.profile-history__btn').on('click', function(e){
			e.stopPropagation();
			e.preventDefault();

			var url = $(this).attr('href');

			$.ajax({
				url: url,
				type: 'post',
				dataType: 'json',
				beforeSend: function() {
				},
				complete: function() {
				},
				success: function(json) {
					// Need to set timeout otherwise it wont update the total
					$('#cart-products-list').load('/index.php?route=common/cart/info', function() {
						$('html, body').animate({ scrollTop: 0 }, 'slow');
						var log = $('.basket-log');

						if(!log.hasClass('opened')){
							log.toggleClass('opened');
						}

						setTimeout(function () {
							$('.basket-log__summa').html( json['total']);
							cart.recalcBasketPrices();
						}, 100);
					});
				}
			});
		});
	},
	init: function(){
		$('.for-popup').magnificPopup();

		this.removeAdress();

		this.noNeedToAddNewAdress();

		this.telephoneMask();

		this.reOrder();
	}
};

$('document').ready(function(){
	Account.init();
});