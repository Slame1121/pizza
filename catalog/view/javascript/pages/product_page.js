var Product = {

	product_id : 0,

	bindIndigrientsInPopUp: function () {
		$('.menu-tabs__link').on('click', function(){
			var self = $(this);
			var group_id = self.data('group-id');
			var option_value_id = $('input.option:checked').data('option-value-id');
			$.ajax({
				url: 'index.php?route=product/product/getIndigrientsByGroup',
				type: 'post',
				data: {id: group_id, option_value_id: option_value_id},
				dataType: 'html',
				beforeSend: function() {
				},
				complete: function() {
				},
				success: function(html) {
					$('.ingredients-popup__range').empty().append(html);
					$('.menu-tabs__link').removeClass('active');
					self.addClass('active');
				}
			});
		});
	},
	bindAddIdigrientsPopUp: function () {
		$('.for-popup').magnificPopup();
		var self = this;
		$('.for-popup').on('click', function(){
			self.recalcPizzaPrice();
		});
		$("input.option").change(function() {
			var size = $(this).parent().find('.card-price__size').html();
			$('.ingredients-popup__item-size').html('('+size+')');
			var price = $(this).parent().find('.card-price__num').html();
			$('.ingredients-popup__item-price').html(price);

			//change attributes prices
			var grou_attr_id = $('.menu-tabs .menu-tabs__link.active').data('group-id');
			var option_value_id = $('input.option:checked').data('option-value-id');
			$.ajax({
				url: 'index.php?route=product/product/getIndigrientsByGroup',
				type: 'post',
				data: {id: grou_attr_id, option_value_id : option_value_id},
				dataType: 'html',
				beforeSend: function() {
				},
				complete: function() {
				},
				success: function(html) {
					$('.ingredients-popup__range').empty().append(html);
				}
			});

			//remove indegrients from add form
			$('#ingredients .ingredients-popup__box >div').remove();
		});



	},
	addIdigrientToPizza: function () {
		var self = this;
		$('#ingredients').on('click', '.ingredients-popup__range-add', function(e){
			e.stopPropagation();
			e.preventDefault();
			var attr_id = $(this).data('attr-id');
			var ingredient_box = $('.ingredients-popup__box');
			var image = $(this).parent().find('img').attr('src');
			var name = $(this).parent().find('.ingredients-popup__range-name').text();
			var price = $(this).parent().find('.ingredients-popup__range-price').data('price');
			var item = '<div class="ingredients-popup__box-item">\
				<input data-price="'+price+'" type="hidden" class="igredient_input" id="input-ingredient-'+attr_id+'" name="attrs['+attr_id+']" value="1" />\
				<div class="ingredients-popup__box-add">+</div>\
				<div class="ingredients-popup__box-img"><img src="'+image+'" alt=""></div>\
				<div class="ingredients-popup__box-name">'+name+' <span class="ingredients-popup__box-name_count hidden">x 1</span></div><a class="ingredients-popup__box-close" href="#"></a>\
				</div>';

			var igredient_input = ingredient_box.find('#input-ingredient-'+attr_id);
			if(igredient_input.length > 0){
				var cur_igredient_box = igredient_input.parent();
				igredient_input.val(parseInt(igredient_input.val()) + 1);
				cur_igredient_box.find('.ingredients-popup__box-name_count').removeClass('hidden').html('x '+igredient_input.val());
			}else{
				ingredient_box.append(item);
			}
			self.recalcPizzaPrice();
		});
	},
	removeIgredientFromPizza: function () {
		var self = this;
		$('#ingredients').on('click','.ingredients-popup__box-close', function(e){
			e.stopPropagation();
			e.preventDefault();
			$(this).parent().remove();

			self.recalcPizzaPrice();
		})
	},
	addProductButton: function () {
		var self = this;
		$('#add_product').on('click', function(e){
			e.stopPropagation();
			e.preventDefault();
			cart.add(self.product_id,1, $(".option").serialize());
		});
	},
	addProductWithIgredients: function () {
		var self = this;
		$('#add_product_with_attr').on('click', function(e){
			e.stopPropagation();
			e.preventDefault();
			var magnificPopup = $.magnificPopup.instance;

			magnificPopup.close();
			cart.add(self.product_id,1, $(".option").serialize(), $('.igredient_input').serialize());

		});
	},


	init : function(){
		this.bindIndigrientsInPopUp();

		this.bindAddIdigrientsPopUp();

		this.addIdigrientToPizza();

		this.removeIgredientFromPizza();

		this.addProductButton();

		this.addProductWithIgredients();
	},

	recalcPizzaPrice: function(){
		var igredients = $('.ingredients-popup__box').find('input.igredient_input');
		var igredients_cost = 0;
		$.each(igredients, function(key,val){
			igredients_cost +=  parseInt($(val).val()) * parseInt($(val).data('price'));
		});

		var pizza_cost = igredients_cost + parseInt($('.card-price__box.active').find('input[name=size-price]').val());

		$('.ingredients-popup__item-price').html(pizza_cost + ' грн');
	}
};


$(document).ready(function(){
	Product.init();
});