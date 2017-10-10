var Catalog = {

	end : false,

	bindAddNewPage: function () {
		var pizza_content = $('.hit__content');
		var form = $('#filter_form');
		var progress = false;
		var self = this;
		var path = form.find('input[name=path]').val();
		$(window).scroll(function() {
			if($(this).scrollTop() > pizza_content.offset().top + pizza_content.height()- 400 && !progress && !self.end) {
				progress = true;
				form.find('input[name=page]').val(parseInt(form.find('input[name=page]').val()) + 1);
				$.ajax( {
					type: "GET",
					url: form.attr( 'action' ) ,
					data: form.serialize(),
					success: function( response ) {
						if(response == ''){
							self.end = true;
						}
						progress = false;
						$('.hit__content').append(response);
					}
				} );
			}
		});
	},
	init: function(){
		this.changeAttrs();

		this.bindAddNewPage();
	},
	getProducts: function () {
		var form = $('#filter_form');
		form.find('input[name=page]').val(1);
		this.end = false;
		$.ajax( {
			type: "GET",
			url: form.attr( 'action' ) + '&page=' + form.find('input[name=page]').val(),
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
