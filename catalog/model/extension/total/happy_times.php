<?php
class ModelExtensionTotalHappyTimes extends Model {
	public function getTotal($totals) {

		$cart_products = $this->cart->getProducts();

		$quantity = 0;
		$discount_cart_id = $this->cart->getDiscountCartId();
		if($discount_cart_id > 0){
			foreach ($cart_products as $product) {

				$this->load->model('catalog/product');
				$categories = $this->model_catalog_product->getProductCategories($product['product_id']);
				$pizza_product= false;
				foreach($categories as $category_id){
					if($category_id == '59'){
						$pizza_product = true;
					}
				}



				$bdate = $this->customer->getBdate();

				$bday_in_this_year = date('Y'). date('-m-d',$bdate );
				$bday_discount = false;
				//3 дня до и после днюшки скидка 15% на все пиццы
				if($pizza_product){
					if(((time() - strtotime($bday_in_this_year)) / 60 / 60 / 24) > 0){
						if(((time() - strtotime($bday_in_this_year)) / 60 / 60 / 24) - 1 < 3){
							$bday_discount = true;
						}
					}else{
						if(abs((time() - strtotime($bday_in_this_year)) / 60 / 60 / 24)  < 3){
							$bday_discount = true;
						}
					}
				}
				//Если не днюшка
				if(!$bday_discount && $discount_cart_id == $product['cart_id']){
					$totals['totals'][] = [
						'code' => 'happy_times',
						'value' => -round($product['total']/2, 2),
						'sort_order' => $this->config->get('total_happy_times_sort_order'),
						'title' => 'Счастливые часы'
					];
					$totals['total'] -= round($product['total']/2, 2);
				}
			}
		}


	}


}