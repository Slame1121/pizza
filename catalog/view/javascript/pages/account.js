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
	init: function(){
		$('.for-popup').magnificPopup();

		this.removeAdress();

		this.noNeedToAddNewAdress();

		this.telephoneMask();
	}
};

$('document').ready(function(){
	Account.init();
});