<?php
class ControllerCommonHeader extends Controller {
	public function index() {
		// Analytics
		$this->load->model('setting/extension');

		$data['analytics'] = array();

		$analytics = $this->model_setting_extension->getExtensions('analytics');

		foreach ($analytics as $analytic) {
			if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
				$data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
			}
		}

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}

		$data['title'] = $this->document->getTitle();

		$data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();

		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');

		$data['cart_count'] = $this->cart->countProducts();
		$data['total'] = $this->cart->getTotal();
		$data['name'] = $this->config->get('config_name');

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		$this->load->language('common/header');

		// Wishlist
		if ($this->customer->isLogged()) {
			$this->load->model('account/wishlist');

			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
		} else {
			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		}
        //Form Login
        $this->load->model('setting/module');
        $setting_info = $this->model_setting_module->getModule('40');
        $data['form_login_action'] = '';
        if($setting_info['status']){
            $data['form_login_ulogin'] = $this->load->controller('extension/module/ulogin', $setting_info);
        }


		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));
        $data['form_login_action'] = $this->url->link('account/login/logins', '', true);
        $data['form_register_action'] = $this->url->link('account/register/registr', '', true);
        $data['form_forgot_action'] = $this->url->link('account/forgotten/forgot', '', true);

        $data['text_auth_phon'] = $this->language->get('text_auth_phon');
         $data['text_auth_phon_smal'] = $this->language->get('text_auth_phon_smal');
         $data['text_auth_phon_code'] = $this->language->get('text_auth_phon_code');
         $data['text_phon_auth_tit'] = $this->language->get('text_phon_auth_tit');
         $data['text_auth_phon_smal_code'] = $this->language->get('text_auth_phon_smal_code');
         $data['text_auth_pass'] = $this->language->get('text_auth_pass');
         $data['text_auth_pass_smal'] = $this->language->get('text_auth_pass_smal');
         $data['text_auth_chek'] = $this->language->get('text_auth_chek');
         $data['text_auth_pass_new'] = $this->language->get('text_auth_pass_new');
         $data['text_auth_for'] = $this->language->get('text_auth_for');
         $data['text_auth_logins'] = $this->language->get('text_auth_logins');
         $data['text_auth_regist'] = $this->language->get('text_auth_regist');
         $data['text_auth_login'] = $this->language->get('text_auth_login');
         $data['text_auth_send'] = $this->language->get('text_auth_send');

		$data['form_login'] = $this->load->view('account/form_login', $data);

		$data['home'] = $this->url->link('common/home');
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['logged'] = $this->customer->isLogged();
		$data['account'] = $this->url->link('account/account', '', true);
		$data['register'] = $this->url->link('account/register', '', true);
		$data['login'] = $this->url->link('account/login', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['transaction'] = $this->url->link('account/transaction', '', true);
		$data['download'] = $this->url->link('account/download', '', true);
		$data['logout'] = $this->url->link('account/logout', '', true);
		$data['logout_home'] = $this->url->link('common/home', '', true);
		$data['shopping_cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', true);
		$data['contact'] = $this->url->link('information/contact');
		$data['telephone'] = $this->config->get('config_telephone');
		$data['telephone2'] = $this->config->get('config_telephone_2');
		
		$data['language'] = $this->load->controller('common/language');
		$data['currency'] = $this->load->controller('common/currency');
		$data['search'] = $this->load->controller('common/search');
		$data['cart'] = $this->load->controller('common/cart');
		$data['menu'] = $this->load->controller('common/menu');
		$data['adaptive_menu'] = $this->load->controller('common/menu/adaptive');
		$data['checkout'] = $this->load->controller('checkout/checkout/checkout');
        $data['styles'] = $this->document->getStyles();
        $data['scripts'] = $this->document->getScripts('header');

        $this->load->model('setting/module');
        $setting_info = $this->model_setting_module->getModule(42);
        $data['header_slogan'] = $this->load->controller('extension/module/html', $setting_info);
        $data['text_title_txt'] = $this->language->get('text_title_txt');
        $data['text_titles'] = $this->language->get('text_titles');

		$this->load->model('localisation/language');

		$data['languages'] = array();

		$results = $this->model_localisation_language->getLanguages();

		foreach ($results as $result) {
			if ($result['status']) {
				$data['languages'][] = array(
					'name' => $result['name'],
					'code' => $result['code']
				);
			}
		}

		$data['language_code'] = $this->session->data['language'];

		$data['language_change_action']= $this->url->link('common/language/language', '', $this->request->server['HTTPS']);
		if (!isset($this->request->get['route'])) {
			$data['redirect'] = $this->url->link('common/home');
		} else {
			$url_data = $this->request->get;

			unset($url_data['_route_']);

			$route = $url_data['route'];

			unset($url_data['route']);

			$url = '';

			if ($url_data) {
				$url = '&' . urldecode(http_build_query($url_data, '', '&'));
			}

			$data['redirect'] = $this->url->link($route, $url, $this->request->server['HTTPS']);
		}

		$data['no_index'] = $this->document->getNoIndex();
        //var_dump($data['header_slogan']);die;
        //$data['header_slogan'] = $this->model_catalog_information->getInformation($information_id);

		return $this->load->view('common/header', $data);
	}
}
