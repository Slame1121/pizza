<?php
class ControllerCheckoutCheckout extends Controller {
	public function index() {


		$this->response->setOutput($this->checkout());
	}


	public function checkout(){
		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			//$this->response->redirect($this->url->link('checkout/cart'));
		}

		// Validate minimum quantity requirements.
		$products = $this->cart->getProducts();

		$this->load->model('catalog/product');
		$pickup_discount = 0;
		$bdate = $this->customer->getBdate();

		$happy_times_cart_id = $this->cart->getDiscountCartId();
		$bday_in_this_year =  date('Y-m-d',$bdate );
		foreach ($products as $key => $product) {
			$product_total = 0;

			$categories = $this->model_catalog_product->getProductCategories( $product['product_id']);

			$bday_discount = false;

			$pizza_product= false;
			foreach($categories as $category_id){
				if($category_id == '59'){
					$pizza_product = true;
				}
			}
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

			if($happy_times_cart_id == 0 &&	!$bday_discount && $pizza_product){
				//Если это пицца, и не днюшка, и не счастливые часы то самовывоз
				$pickup_discount += $products[$key]['price'] * 0.1 * $products[$key]['quantity'];
			}
			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				//$this->response->redirect($this->url->link('checkout/cart'));
			}
		}
		$this->load->language('checkout/checkout');

		$this->document->setTitle($this->language->get('heading_title'));

		//$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
		//$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
		//$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		//$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

		// Required by klarna
		if ($this->config->get('payment_klarna_account') || $this->config->get('payment_klarna_invoice')) {
			//$this->document->addScript('http://cdn.klarna.com/public/kitt/toc/v1.0/js/klarna.terms.min.js');
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_cart'),
			'href' => $this->url->link('checkout/cart')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('checkout/checkout', '', true)
		);
		$data['want_to_use'] = isset($this->session->data['guest']['used_points']) ? $this->session->data['guest']['used_points'] : 0;
		$data['total_cart_price_def'] = $this->cart->getTotal();
		$data['total_cart_price'] = $this->cart->getTotal() - $data['want_to_use'] ;

		if(isset($this->session->data['shipping_method']['code'])){
			$data['shipping_method_code'] = $this->session->data['shipping_method']['code'];
		}else{

			if($this->customer->isLogged()){
				$data['shipping_method_code']= $this->customer->last_shipping_method;
			}else{
				$data['shipping_method_code']= '';
			}
		}

		//10% на самовывоз
		if (isset($data['shipping_method_code']) && $data['shipping_method_code'] == 'pickup') {
			$data['total_cart_price'] -= $data['total_cart_price'] * 0.1;

		}
		$data['pickup_discount'] = $pickup_discount;
		$data['text_checkout_option'] = sprintf($this->language->get('text_checkout_option'), 1);
		$data['text_checkout_account'] = sprintf($this->language->get('text_checkout_account'), 2);
		$data['text_checkout_payment_address'] = sprintf($this->language->get('text_checkout_payment_address'), 2);
		$data['text_checkout_shipping_address'] = sprintf($this->language->get('text_checkout_shipping_address'), 3);
		$data['text_checkout_shipping_method'] = sprintf($this->language->get('text_checkout_shipping_method'), 4);

		$data['shipping_method'] = $this->load->controller('checkout/shipping_method');
		$data['payment_method'] = $this->load->controller('checkout/payment_method');
		$data['shipping_adresses'] = $this->load->controller('checkout/shipping_address');
		if ($this->cart->hasShipping()) {
			$data['text_checkout_payment_method'] = sprintf($this->language->get('text_checkout_payment_method'), 5);
			$data['text_checkout_confirm'] = sprintf($this->language->get('text_checkout_confirm'), 6);
		} else {
			$data['text_checkout_payment_method'] = sprintf($this->language->get('text_checkout_payment_method'), 3);
			$data['text_checkout_confirm'] = sprintf($this->language->get('text_checkout_confirm'), 4);
		}

		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} else {
			$data['error_warning'] = '';
		}

		$data['logged'] = $this->customer->isLogged();

		if (isset($this->session->data['account'])) {
			$data['account'] = $this->session->data['account'];
		} else {
			$data['account'] = '';
		}

		$data['shipping_required'] = $this->cart->hasShipping();

		if(isset($this->session->data['guest']['email'])){
			$data['email'] = $this->session->data['guest']['email'];
		}else{
			if($this->customer->isLogged()){

				$data['email'] = $this->customer->getEmail();
			}else{
				$data['email'] = '';
			}

		}
		$data['comment'] = isset($this->session->data['comment']) ? $this->session->data['comment'] : '';
		$data['firstname'] = isset($this->session->data['guest']['firstname']) ? $this->session->data['guest']['firstname'] : $this->customer->getFirstName();
		$data['telephone'] = isset($this->session->data['guest']['telephone']) ? $this->session->data['guest']['telephone'] : $this->customer->getTelephone();
		$data['bonuses'] = isset($this->customer->bonuses) ? ($this->customer->bonuses) : 0;

		$data['can_get_bonuses'] = $this->cart->getTotal() * 0.05;


		//$data['column_left'] = $this->load->controller('common/column_left');
		//$data['column_right'] = $this->load->controller('common/column_right');
		//$data['content_top'] = $this->load->controller('common/content_top');
		//$data['content_bottom'] = $this->load->controller('common/content_bottom');
		//$data['footer'] = $this->load->controller('common/footer');
		//$data['header'] = $this->load->controller('common/header');

		return $this->load->view('checkout/checkout', $data);
	}

	public function checkbonuses(){
		if(isset($_POST['bonuses'])){
			$bon = (float)$_POST['bonuses'];
			if(isset($this->customer->bonuses ) && $this->customer->bonuses >= $bon){
				$this->response->setOutput((1));
			}else{
				if(!isset($this->customer->bonuses) && $bon == 0){
					$this->response->setOutput((1));
				}else{
					$this->response->setOutput((0));
				}
			}
		}
	}

	public function country() {
		$json = array();

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


	public function save(){
		$this->session->data['account'] = 'guest';
		if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $this->request->post['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}
		$this->session->data['guest']['customer_group_id'] = $customer_group_id;
		$this->session->data['guest']['firstname'] = $this->request->post['firstname'];
		$this->session->data['guest']['email'] = $this->request->post['email'];
		$this->session->data['guest']['nominal'] = isset($this->request->post['nominal']) ? $this->request->post['nominal'] : 0;
		$this->session->data['guest']['telephone'] = $this->request->post['telephone'];
		$this->session->data['guest']['used_points'] = isset($this->request->post['used_points']) ? (int)$this->request->post['used_points'] : 0;
		$this->load->model('account/address');

		$code = explode('.',$this->request->post['shipping_method'])[0];
		$this->session->data['shipping_method']['code'] = $code;
		if(isset($this->session->data['shipping_methods'][$code])){
			$shipping_method_title = $this->session->data['shipping_methods'][$code]['title'];
		}else{
			$shipping_method_title = '';
		}
		if($code == 'pickup'){
			$cart_products = $this->cart->getProducts();

			$this->load->model('catalog/product');
			$happy_time_discount = $this->cart->getDiscountCartId();
			$pickup_discount = 0;
			$bdate = $this->customer->getBdate();

			$bday_in_this_year = date('Y-m-d',$bdate );
			$bday_discount = false;
			foreach ($cart_products as $product) {


				$categories = $this->model_catalog_product->getProductCategories($product['product_id']);
				$pizza_product= false;
				foreach($categories as $category_id){
					if($category_id == '59'){
						$pizza_product = true;
					}
				}


				//3 дня до и после днюшки скидка 15% на все пиццы
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

			if($happy_time_discount == 0 && !$bday_discount){
				$this->session->data['shipping_method']['cost'] = -$pickup_discount;
			}

		}else{
			$this->session->data['shipping_method']['cost'] = 0;
		}
		if(isset($this->request->post['address_id'])){
			$adress = $this->model_account_address->getAddress((int)$this->request->post['address_id']);
			$this->session->data['shipping_address']['nas_punkt'] = $adress['nas_punkt'];
			$this->session->data['shipping_address']['street'] = $adress['street'];
			$this->session->data['shipping_address']['house'] = $adress['house'];
			$this->session->data['shipping_address']['paradnya'] = $adress['paradnya'];
			$this->session->data['shipping_address']['floor'] = $adress['floor'];
			$this->session->data['shipping_address']['flat'] = $adress['flat'];
			$this->session->data['shipping_address']['code_door'] = $adress['code_door'];
		}else{
			$this->session->data['shipping_address']['nas_punkt'] = $this->request->post['nas_punkt'];
			$this->session->data['shipping_address']['street'] = $this->request->post['street'];
			$this->session->data['shipping_address']['house'] = $this->request->post['house'];
			$this->session->data['shipping_address']['paradnya'] = isset($this->request->post['paradnya']) ? $this->request->post['paradnya'] : '';
			$this->session->data['shipping_address']['floor'] = isset($this->request->post['floor']) ? $this->request->post['floor'] : '';
			$this->session->data['shipping_address']['flat'] = isset($this->request->post['flat']) ? $this->request->post['flat'] : '';
			$this->session->data['shipping_address']['code_door'] =isset($this->request->post['code_door']) ? $this->request->post['code_door'] : '';
			if($this->customer->isLogged()){
				if($this->session->data['shipping_method']['code'] != 'pickup'){
					$this->model_account_address->addAddress($this->customer->getId(), $this->session->data['shipping_address']);
				}

			}

		}






		$this->session->data['shipping_method']['title'] = $shipping_method_title;

		$this->session->data['comment'] = $this->request->post['comment'];
		$this->session->data['payment_method']['code'] = $this->request->post['payment_method'];

		if(isset($this->session->data['payment_methods'][$this->request->post['payment_method']])){
			$payment_method_title = $this->session->data['payment_methods'][$this->request->post['payment_method']]['title'];
		}else{
			$payment_method_title = '';
		}
		$this->session->data['payment_method']['title'] = $payment_method_title;
		$json = ['success' => 1];



		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function customfield() {
		$json = array();

		$this->load->model('account/custom_field');

		// Customer Group
		if (isset($this->request->get['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->get['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $this->request->get['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

		foreach ($custom_fields as $custom_field) {
			$json[] = array(
				'custom_field_id' => $custom_field['custom_field_id'],
				'required'        => $custom_field['required']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}