<?php
class ControllerAccountAccount extends Controller {
	public function index() {

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/account', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}
        $this->document->addStyle('/catalog/view/theme/default/stylesheet/bootstrap-datepicker3.min.css');
		$this->document->addScript('/catalog/view/javascript/pages/account.js');
		$this->document->addScript('/catalog/view/javascript/jquery-migrate-3.0.1.min.js');
		$this->document->addScript('/catalog/view/javascript/bootstrap-datepicker.js');
        $data['langs'] = 'ru';
		if($this->language->data["code"] == "uk"){
            $data['langs'] = 'uk';
            $this->document->addScript('/catalog/view/javascript/locales/bootstrap-datepicker.uk.js');
        }else{
            $this->document->addScript('/catalog/view/javascript/locales/bootstrap-datepicker.ru.js');
        }

		$this->load->language('account/account');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		} 
		
		$data['edit'] = $this->url->link('account/edit', '', true);
		$data['password'] = $this->url->link('account/password', '', true);
		$data['address'] = $this->url->link('account/address', '', true);
		$this->load->model('account/address');
		$data['addresses'] = $this->model_account_address->getAddresses();
		$data['credit_cards'] = array();
        $data['text_acc_hello'] = $this->language->get('text_acc_hello');
        $data['text_acc_user_text'] = $this->language->get('text_acc_user_text');
        $data['text_acc_user_name'] = $this->language->get('text_acc_user_name');
        $data['text_acc_user_tel'] = $this->language->get('text_acc_user_tel');
        $data['text_acc_user_date'] = $this->language->get('text_acc_user_date');
        $data['text_acc_user_pass'] = $this->language->get('text_acc_user_pass');
        $data['text_acc_user_new_pass'] = $this->language->get('text_acc_user_new_pass');
        $data['text_acc_user_place'] = $this->language->get('text_acc_user_place');
        $data['text_acc_user_btn_tit'] = $this->language->get('text_acc_user_btn_tit');

        $data['text_acc_user_adr'] = $this->language->get('text_acc_user_adr');
        $data['text_acc_user_adr_add'] = $this->language->get('text_acc_user_adr_add');
        $data['text_acc_user_sity'] = $this->language->get('text_acc_user_sity');
        $data['arr_sity'] = $this->language->get('text_acc_user_arr_sity');
        $data['text_acc_user_strit'] = $this->language->get('text_acc_user_strit');
        $data['text_acc_user_dim'] = $this->language->get('text_acc_user_dim');
        $data['text_acc_user_home'] = $this->language->get('text_acc_user_home');
        $data['text_acc_user_lvl'] = $this->language->get('text_acc_user_lvl');
        $data['text_acc_user_home_nom'] = $this->language->get('text_acc_user_home_nom');
        $data['text_acc_user_key_door'] = $this->language->get('text_acc_user_key_door');

		$files = glob(DIR_APPLICATION . 'controller/extension/credit_card/*.php');
		
		foreach ($files as $file) {
			$code = basename($file, '.php');
			
			if ($this->config->get('payment_' . $code . '_status') && $this->config->get('payment_' . $code . '_card')) {
				$this->load->language('extension/credit_card/' . $code, 'extension');

				$data['credit_cards'][] = array(
					'name' => $this->language->get('extension')->get('heading_title'),
					'href' => $this->url->link('extension/credit_card/' . $code, '', true)
				);
			}
		}
		
		$data['wishlist'] = $this->url->link('account/wishlist');
		$data['order'] = $this->url->link('account/order', '', true);
		$data['download'] = $this->url->link('account/download', '', true);
		
		if ($this->config->get('total_reward_status')) {
			$data['reward'] = $this->url->link('account/reward', '', true);
		} else {
			$data['reward'] = '';
		}		
		
		$data['return'] = $this->url->link('account/return', '', true);
		$data['transaction'] = $this->url->link('account/transaction', '', true);
		$data['newsletter'] = $this->url->link('account/newsletter', '', true);
		$data['recurring'] = $this->url->link('account/recurring', '', true);
		
		$this->load->model('account/customer');
		
		$affiliate_info = $this->model_account_customer->getAffiliate($this->customer->getId());
		
		if (!$affiliate_info) {	
			$data['affiliate'] = $this->url->link('account/affiliate/add', '', true);
		} else {
			$data['affiliate'] = $this->url->link('account/affiliate/edit', '', true);
		}


			$data['form_action'] = $this->url->link('account/account/save', '', true);

		
		if ($affiliate_info) {		
			$data['tracking'] = $this->url->link('account/tracking', '', true);
		} else {
			$data['tracking'] = '';
		}

		$data['email'] = $this->customer->getEmail();
		$data['telephone'] = $this->customer->getTelephone();
		$data['firstname'] = $this->customer->getFirstName();
		$data['bdate'] = date( "d.m.Y", $this->customer->getBdate() );
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$data['orders_list'] = $this->load->controller('account/order');
		
		$this->response->setOutput($this->load->view('account/account', $data));
	}

	public function save(){
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {


			$this->load->model('account/address');
			$this->load->model('account/customer');
			if(isset($this->request->post['nas_punkt'])){
				$this->model_account_address->addAddress($this->customer->getId(), $this->request->post);
			}


			$customer_info = [
				'telephone' => $this->request->post['telephone'],
				'email' => $this->request->post['email'],
				'firstname' => $this->request->post['firstname'],
                'bdate' => $this->request->post['bdate']
			];
			$this->model_account_customer->editCustomer($this->customer->getId(), $customer_info);

			$this->response->redirect($this->url->link('account/account', '', true));
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

	private function validateForm()
	{
		return true;
	}
}
