<?php
class ControllerCommonCart extends Controller {
	public function index() {
		$this->load->language('common/cart');

		// Totals
		$this->load->model('setting/extension');

		$totals = array();
		$taxes = $this->cart->getTaxes();
		$total = 0;

		// Because __call can not keep var references so we put them into an array.
		$total_data = array(
			'totals' => &$totals,
			'taxes'  => &$taxes,
			'total'  => &$total
		);
			
		// Display prices
		if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
			$sort_order = array();

			$results = $this->model_setting_extension->getExtensions('total');

			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
			}

			array_multisort($sort_order, SORT_ASC, $results);

			foreach ($results as $result) {
				if ($this->config->get('total_' . $result['code'] . '_status')) {
					$this->load->model('extension/total/' . $result['code']);

					// We have to put the totals in an array so that they pass by reference.
					$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
				}
			}

			$sort_order = array();

			foreach ($totals as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $totals);
		}



		$this->load->model('tool/image');
		$this->load->model('tool/upload');

		$data['products'] = array();
		$this->load->model('catalog/catalog');

		$all_attrs = $this->model_catalog_catalog->getAllAtributes();
		$pretedends_for_discount = [
		];
		$cart_products = $this->cart->getProducts();
		$quantity = 0;
		foreach ($cart_products as $product) {
			if ($product['image']) {
				$image = $this->model_tool_image->resize($product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_height'));
			} else {
				$image = '';
			}

			$option_data = array();

			foreach ($product['option'] as $option) {
				if ($option['type'] != 'file') {
					$value = $option['value'];
				} else {
					$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

					if ($upload_info) {
						$value = $upload_info['name'];
					} else {
						$value = '';
					}
				}

				$option_data[] = array(
					'name'  => $option['name'],
					'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value),
					'type'  => $option['type']
				);
			}

			// Display prices
			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$unit_price = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'));
				
				$price = $unit_price;
				$total = $this->currency->format($unit_price * $product['quantity'], $this->session->data['currency']);
			} else {
				$price = false;
				$total = false;
			}
			$attributes = [];

			foreach($product['attrs'] as $igredient_id => $count){
				$attributes[] = [
					'id' => $igredient_id,
					'name' => $all_attrs[$igredient_id]['name'],
					'count' => $count
				];
			}
			$this->load->model('catalog/product');
			$categories = $this->model_catalog_product->getProductCategories($product['product_id']);
			$pizza_product= false;
			foreach($categories as $category_id){
				if($category_id == '59'){
					$pizza_product = true;
				}
			}
			$data['products'][] = array(
				'cart_id'   => $product['cart_id'],
				'thumb'     => $image,
				'name'      => $product['name'],
				'model'     => $product['model'],
				'option'    => $option_data,
				'recurring' => ($product['recurring'] ? $product['recurring']['name'] : ''),
				'quantity'  => $product['quantity'],
				'price'     => $price,
				'total'     => $total,
				'attrs'     => $attributes,
				'discount_pizza' => 0,
				'href'      => $this->url->link('product/product', 'product_id=' . $product['product_id'])
			);
			$days = [
				'Sunday',
				'Monday',
				'Tuesday',
				'Wednesday',
				'Thursday',
				'Friday',
				'Saturday'
			];
			//Если это пицца и дело происходит в понедельник, вторник, среду или четвер между 12 утра и 4 вечера
			if($pizza_product && in_array(jddayofweek(cal_to_jd(CAL_GREGORIAN, date("m"),date("d"), date("Y"))),[1,2,3,4])  /*&& time() < strtotime('today 4:00:00 pm') && time() > strtotime('today 00:00:00 am')*/){
				for( $i = 1; $i <= $product['quantity']; $i++){
					$pretedends_for_discount[] =['price' => $price, 'key' => count($data['products']) - 1];
				}
			}
			$quantity += $product['quantity'];
		}

		$data['text_items'] = $quantity;

		if(count($pretedends_for_discount) > 1){
			while(count($pretedends_for_discount) > 1){
				if($pretedends_for_discount[0]['price'] >= $pretedends_for_discount[1]['price']){
					array_splice($pretedends_for_discount, 0, 1);
				}else{
					$next = $pretedends_for_discount[1];
					$pretedends_for_discount[1] =  $pretedends_for_discount[0];
					$pretedends_for_discount[0] = $next;
				}
			}

			if($pretedends_for_discount){
				if(isset($data['products'][$pretedends_for_discount[0]['key']])){
					$data['products'][$pretedends_for_discount[0]['key']]['discount_pizza'] = 1;
					$total = round($data['products'][$pretedends_for_discount[0]['key']]['price']/2,2) + $data['products'][$pretedends_for_discount[0]['key']]['price'] * ($data['products'][$pretedends_for_discount[0]['key']]['quantity'] - 1);

					$data['products'][$pretedends_for_discount[0]['key']]['total'] = $this->currency->format($total, $this->session->data['currency']);

				}
			}
		}

		// Gift Voucher
		$data['vouchers'] = array();

		if (!empty($this->session->data['vouchers'])) {
			foreach ($this->session->data['vouchers'] as $key => $voucher) {
				$data['vouchers'][] = array(
					'key'         => $key,
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'], $this->session->data['currency'])
				);
			}
		}

		$data['totals'] = array();

		foreach ($totals as $total) {
			$data['totals'][] = array(
				//'title' => $total['title'],
				//'text'  => $this->currency->format($total['value'], $this->session->data['currency']),
			);
		}

		$data['total'] = $total_data['total'];

		$data['cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', true);

		return $this->load->view('common/cart', $data);
	}

	public function info() {
		$this->response->setOutput($this->index());
	}
}