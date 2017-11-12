<?php
class ModelExtensionTotalShipping extends Model {
	public function getTotal($total) {

		$this->load->model('catalog/product');
		$happy_time_discount = $this->cart->getDiscountCartId();
		$pickup_discount = 0;
		$bdate = $this->customer->getBdate();

		$bday_in_this_year = date('Y').date('-m-d',$bdate );
		$bday_discount = false;
		$cart_products = $this->cart->getProducts();
		foreach ($cart_products as $product) {
			$categories = $this->model_catalog_product->getProductCategories($product['product_id']);
			$pizza_product= false;
			foreach($categories as $category_id){
				if($category_id == '59'){
					$pizza_product = true;
				}
			}
			if($pizza_product){
				//Заодно расчитаем стоимость этой доставки заранее
				$pickup_discount += $product['total'] * 0.1;

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
		}
		if($happy_time_discount == 0 && !$bday_discount && $pickup_discount > 0){
			$total['totals'][] = array(
				'code'       => 'shipping',
				'title'      => 'Самовывооз',
				'value'      => -$pickup_discount,
				'sort_order' => $this->config->get('total_shipping_sort_order')
			);


			$total['total'] -= $pickup_discount;
		}

	}
}