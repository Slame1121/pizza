var Catalog = {
	bindAddNewPage: function () {
		var pizza_content = $('.hit__content');
		var form = $('#filter_form');
		var progress = false;
		var end = false;
		var path = form.find('input[name=path]').val();
		$(window).scroll(function() {
			if($(this).scrollTop() > pizza_content.offset().top + pizza_content.height()- 700 && !progress && !end) {
				progress = true;
				form.find('input[name=page]').val(parseInt(form.find('input[name=page]').val()) + 1);
				$.ajax( {
					type: "POST",
					url: form.attr( 'action' ) + '&page=' + form.find('input[name=page]').val() + '&path='+path,
					data: form.serialize(),
					success: function( response ) {
						if(response == ''){
							end = true;
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
		$.ajax( {
			type: "POST",
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
