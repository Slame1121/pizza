<?php
class ModelExtensionTotalBirthday extends Model {
	public function getTotal($total) {

		$cart_products = $this->cart->getProducts();

		$discount = 0;
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

			if($bday_discount){
				$discount += $product['total'] * 0.15;
			}
		}
		if($discount > 0){
			$total['totals'][] = array(
				'code'       => 'total',
				'title'      => 'День рождения',
				'value'      => -$discount,
				'sort_order' => $this->config->get('total_birthday_sort_order')
			);

			$total['total'] -= $discount;
		}

	}
}