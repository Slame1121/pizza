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

		$happy_times_cart_id = $this->cart->getDiscountCartId();
		$this->load->model('catalog/category');
		$all_categories = $this->model_catalog_category->getCategories();
		$categories_data = [];
		foreach($all_categories as $category){
			$categories_data[$category['category_id']] = $category;
		}
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
			$bday_discount_config = false;
			foreach($categories as $category_id){
				if($categories_data[$category_id]['birthday']){
					$bday_discount_config = true;
				}
			}

			$bdate = $this->customer->getBdate();

			$bday_in_this_year = date('Y').date('-m-d',$bdate );
			$bday_discount = false;
			//3 дня до и после днюшки скидка 15% на все пиццы
			if($bday_discount_config){

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



			$data['products'][] = array(
				'cart_id'   => $product['cart_id'],
				'thumb'     => $image,
				'name'      => $product['name'],
				'model'     => $product['model'],
				'option'    => $option_data,
				'b_day'     => $bday_discount,
				'recurring' => ($product['recurring'] ? $product['recurring']['name'] : ''),
				'quantity'  => $product['quantity'],
				'price'     => $price,
				'total'     => $total,
				'attrs'     => $attributes,
				'discount_pizza' => (!$bday_discount && $product['cart_id'] == $happy_times_cart_id) ? 1 : 0,
				'href'      => $this->url->link('product/product', 'product_id=' . $product['product_id'])
			);

			$quantity += $product['quantity'];
		}

		$data['text_items'] = $quantity;


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