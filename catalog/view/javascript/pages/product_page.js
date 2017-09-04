var Product = {
	bindIndigrientsInPopUp: function () {
		$('.menu-tabs__link').on('click', function(){
			var self = $(this);
			var group_id = self.data('group-id');
			$.ajax({
				url: 'index.php?route=product/product/getIndigrientsByGroup',
				type: 'post',
				data: {id: group_id},
				dataType: 'html',
				beforeSend: function() {
				},
				complete: function() {
				},
				success: function(html) {
					console.log(1111111);
					$('.ingredients-popup__range').empty().append(html);
					$('.menu-tabs__link').removeClass('active');
					self.addClass('active');
				}
			});
		});
	},
	init : function(){
		this.bindIndigrientsInPopUp();
	}
};


$(document).ready(function(){
	Product.init();
});