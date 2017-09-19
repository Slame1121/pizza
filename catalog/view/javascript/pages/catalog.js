var Catalog = {
	init: function(){
		this.changeAttrs();
	},
	getProducts: function () {
		var form = $('#filter_form');
		$.ajax( {
			type: "POST",
			url: form.attr( 'action' ),
			data: form.serialize(),
			success: function( response ) {
				$('.hit__content').empty().html(response);
			}
		} );
	},
	changeAttrs: function(){
		var form = $('#filter_form');
		var self = this;
		form.on('change', 'input', function(){
			self.getProducts();
		})
	}
};
$(document).ready(function(){
	Catalog.init();
});
