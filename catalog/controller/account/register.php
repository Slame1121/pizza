<?php
class ControllerAccountRegister extends Controller {
	private $error = array();

	public function index() {
		if ($this->customer->isLogged()) {
			$this->response->redirect($this->url->link('account/account', '', true));
		}

		$this->load->language('account/register');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

		$this->load->model('account/customer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			$customer_id = $this->model_account_customer->addCustomer($this->request->post);
			// Clear any previous login attempts for unregistered accounts.
			$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);

			$this->customer->login($this->request->post['email'], $this->request->post['password']);

			unset($this->session->data['guest']);

			$this->response->redirect($this->url->link('account/success'));
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_register'),
			'href' => $this->url->link('account/register', '', true)
		);
		$data['text_account_already'] = sprintf($this->language->get('text_account_already'), $this->url->link('account/login', '', true));

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['firstname'])) {
			$data['error_firstname'] = $this->error['firstname'];
		} else {
			$data['error_firstname'] = '';
		}

		if (isset($this->error['lastname'])) {
			$data['error_lastname'] = $this->error['lastname'];
		} else {
			$data['error_lastname'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['telephone'])) {
			$data['error_telephone'] = $this->error['telephone'];
		} else {
			$data['error_telephone'] = '';
		}

		if (isset($this->error['custom_field'])) {
			$data['error_custom_field'] = $this->error['custom_field'];
		} else {
			$data['error_custom_field'] = array();
		}

		if (isset($this->error['password'])) {
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}

		if (isset($this->error['confirm'])) {
			$data['error_confirm'] = $this->error['confirm'];
		} else {
			$data['error_confirm'] = '';
		}

		$data['action'] = $this->url->link('account/register', '', true);

		$data['customer_groups'] = array();

		if (is_array($this->config->get('config_customer_group_display'))) {
			$this->load->model('account/customer_group');

			$customer_groups = $this->model_account_customer_group->getCustomerGroups();

			foreach ($customer_groups as $customer_group) {
				if (in_array($customer_group['customer_group_id'], $this->config->get('config_customer_group_display'))) {
					$data['customer_groups'][] = $customer_group;
				}
			}
		}

		if (isset($this->request->post['customer_group_id'])) {
			$data['customer_group_id'] = $this->request->post['customer_group_id'];
		} else {
			$data['customer_group_id'] = $this->config->get('config_customer_group_id');
		}

		if (isset($this->request->post['firstname'])) {
			$data['firstname'] = $this->request->post['firstname'];
		} else {
			$data['firstname'] = '';
		}

		if (isset($this->request->post['lastname'])) {
			$data['lastname'] = $this->request->post['lastname'];
		} else {
			$data['lastname'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		} else {
			$data['telephone'] = '';
		}

		// Custom Fields
		$data['custom_fields'] = array();
		
		$this->load->model('account/custom_field');
		
		$custom_fields = $this->model_account_custom_field->getCustomFields();
		
		foreach ($custom_fields as $custom_field) {
			if ($custom_field['location'] == 'account') {
				$data['custom_fields'][] = $custom_field;
			}
		}
		
		if (isset($this->request->post['custom_field']['account'])) {
			$data['register_custom_field'] = $this->request->post['custom_field']['account'];
		} else {
			$data['register_custom_field'] = array();
		}

		if (isset($this->request->post['password'])) {
			$data['password'] = $this->request->post['password'];
		} else {
			$data['password'] = '';
		}

		if (isset($this->request->post['confirm'])) {
			$data['confirm'] = $this->request->post['confirm'];
		} else {
			$data['confirm'] = '';
		}

		if (isset($this->request->post['newsletter'])) {
			$data['newsletter'] = $this->request->post['newsletter'];
		} else {
			$data['newsletter'] = '';
		}

		// Captcha
		if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('register', (array)$this->config->get('config_captcha_page'))) {
			$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'), $this->error);
		} else {
			$data['captcha'] = '';
		}

		if ($this->config->get('config_account_id')) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

			if ($information_info) {
				$data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_account_id'), true), $information_info['title'], $information_info['title']);
			} else {
				$data['text_agree'] = '';
			}
		} else {
			$data['text_agree'] = '';
		}

		if (isset($this->request->post['agree'])) {
			$data['agree'] = $this->request->post['agree'];
		} else {
			$data['agree'] = false;
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/register', $data));
	}

	public function registr(){
        if ($this->customer->isLogged()) {
            $this->response->redirect($this->url->link('account/account', '', true));
        }
        $this->load->language('account/register');
        $json = array();
        $this->load->model('account/customer');
        $data['redirect'] = false;
        $data['error'] = false;
        $data['success'] = false;

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validat()) {

            $customer_id = $this->model_account_customer->addCustomer($this->request->post);
            $this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
            $this->customer->login($this->request->post['email'], $this->request->post['password']);
            unset($this->session->data['guest']);
            $data['redirect'] = $this->url->link('account/account');//account/success
        }else{
            if (isset($this->error['email'])) {
                $json['error'][] = ['inp' => "input[name='email']" , 'text' => $this->error['email']];
            }
            if (isset($this->error['telephone'])) {
                $json['error'][] = ['inp' => "input[name='tel']" , 'text' => $this->error['telephone']];
            }
            if (isset($this->error['password'])) {
                $json['error'][] = ['inp' => "input[name='password']" , 'text' => $this->error['password']];
            }
            if (isset($this->error['warning'])) {
                if (isset($this->error['warning']['email'])) {
                    $json['error'][] = ['inp' => "input[name='email']" , 'text' => $this->error['warning']['email']];
                }
                if (isset($this->error['warning']['tel'])) {
                    $json['error'][] = ['inp' => "input[name='tel']" , 'text' => $this->error['warning']];
                }
                //$json['error'][] = ['inp' => "input[name='email']" , 'text' => $this->error['warning']['tel']];
            }
            if (isset($this->error['tel_code'])) {
                $json['error'][] = ['inp' => "input[name='tel_code']" , 'text' => $this->error['tel_code']];
            }

        }
        //var_dump($this->error);die;
        if (!isset($json['error'])) {
            $json['success'] = $data;
        }else{
            $json['success'] = false;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function send(){
        if ($this->customer->isLogged()) {
            $this->response->redirect($this->url->link('account/account', '', true));
        }
        $this->load->language('account/register');
        $this->load->model('account/customer');

        $json = array();
        $json['redirect'] = false;
        $json['error'] = false;
        $json['success'] = false;

        if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
            $json['error'][] = ['inp' => "input[name='email']" , 'text' => $this->language->get('error_email')];
        }

        if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
            $json['error'][] = ['inp' => "input[name='email']" , 'text' => $this->language->get('error_exists')];
        }

        if ($this->model_account_customer->getCustomerByTel($this->request->post['phone'])) {
            $json['error'][] = ['inp' => "input[name='tel']" , 'text' => $this->language->get('error_exists_tel')];
        }

        if ((utf8_strlen($this->request->post['phone']) < 3) || (utf8_strlen($this->request->post['phone']) > 32)) {
            $json['error'][] = ['inp' => "input[name='tel']" , 'text' => $this->language->get('error_telephone')];
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && !$json['error']) {
            $tel = isset($this->request->post['phone'])?$this->request->post['phone']: false;
            if($tel){
                $tel = str_replace(' ','',$tel);
                $tel = str_replace('+3','',$tel);

                $code  = mt_rand(1000,9999);
                $json['send'] = $this->sendSMS($tel,$code);
                $json['code']  = md5($tel.$code);
                //$json['code_v']  = $code;
                $json['success'] = true;

            }else{
                $json['error'][] = ['inp' => "input[name='tel']" , 'text' => $this->error['telephone']];
            }
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }


	private function validat() {
//		if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
//			$this->error['firstname'] = $this->language->get('error_firstname');
//		}
//
//		if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
//			$this->error['lastname'] = $this->language->get('error_lastname');
//		}

		if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
			$this->error['warning']['email'] = $this->language->get('error_exists');
		}

        if ($this->model_account_customer->getCustomerByTel($this->request->post['tel'])) {
			$this->error['warning']['tel'] = $this->language->get('error_exists_tel');
		}

		if ((utf8_strlen($this->request->post['tel']) < 3) || (utf8_strlen($this->request->post['tel']) > 32)) {
			$this->error['tel'] = $this->language->get('error_telephone');
		}

		if ((utf8_strlen($this->request->post['sms_rec']) < 30) || (utf8_strlen($this->request->post['sms_rec']) > 32)) {
			$this->error['tel_code'] = $this->language->get('error_tel_code');
		}else{
		    if(!isset($this->error['tel'])){

                if (utf8_strlen($this->request->post['tel_code']) != 4) {
                    $this->error['tel_code'] = $this->language->get('error_tel_code_len');
                }else{
                    $code_hash = $this->request->post['sms_rec'];
                    $tel = $this->request->post['tel'];
                    $tel = str_replace(' ','',$tel);
                    $tel = str_replace('+3','',$tel);

                    $code = $this->request->post['tel_code'];
                    $hash = md5($tel.$code);
                    if($hash != $code_hash){
                        $this->error['tel_code'] = $this->language->get('error_tel_code_err');
                    }
                }
            }
        }

		// Customer Group
//		if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
//			$customer_group_id = $this->request->post['customer_group_id'];
//		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		//}

		// Custom field validation
		$this->load->model('account/custom_field');

		$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

		foreach ($custom_fields as $custom_field) {
			if ($custom_field['location'] == 'account') {
				if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
					$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
				} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
					$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
				}
			}
		}

		if ((utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
			$this->error['password'] = $this->language->get('error_password');
		}

//		if ($this->request->post['confirm'] != $this->request->post['password']) {
//			$this->error['confirm'] = $this->language->get('error_confirm');
//		}

		// Captcha
		if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('register', (array)$this->config->get('config_captcha_page'))) {
			$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

			if ($captcha) {
				$this->error['captcha'] = $captcha;
			}
		}

		// Agree to terms
		if ($this->config->get('config_account_id')) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

//			if ($information_info && !isset($this->request->post['agree'])) {
//				$this->error['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
//			}
		}

		return !$this->error;
	}

	private function validate() {
		if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}

		if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
			$this->error['lastname'] = $this->language->get('error_lastname');
		}

		if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
			$this->error['warning'] = $this->language->get('error_exists');
		}

		if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
			$this->error['telephone'] = $this->language->get('error_telephone');
		}

		// Customer Group
		if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $this->request->post['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		// Custom field validation
		$this->load->model('account/custom_field');

		$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

		foreach ($custom_fields as $custom_field) {
			if ($custom_field['location'] == 'account') {
				if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
					$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
				} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
					$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
				}
			}
		}

		if ((utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
			$this->error['password'] = $this->language->get('error_password');
		}

		if ($this->request->post['confirm'] != $this->request->post['password']) {
			$this->error['confirm'] = $this->language->get('error_confirm');
		}

		// Captcha
		if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('register', (array)$this->config->get('config_captcha_page'))) {
			$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

			if ($captcha) {
				$this->error['captcha'] = $captcha;
			}
		}

		// Agree to terms
		if ($this->config->get('config_account_id')) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

			if ($information_info && !isset($this->request->post['agree'])) {
				$this->error['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
			}
		}

		return !$this->error;
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

	private function getBallans(){
        $user = SMS_USER;
        $password = SMS_PASS;

        $myXML 	 = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $myXML 	.= "<request>"."\n";
        $myXML 	.= "<operation>GETBALANCE</operation>"."\n";
        $myXML 	.= "</request>";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERPWD , $user.':'.$password);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, 'http://sms-fly.com/api/api.noai.php');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml", "Accept: text/xml"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $myXML);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function sendSMS($tel,$text,$desc = 'send'){
        $text = iconv('windows-1251', 'utf-8', htmlspecialchars($text));
        $description = iconv('windows-1251', 'utf-8', htmlspecialchars($desc));
        $start_time = 'AUTO'; //отправить немедленно
        $end_time = 'AUTO'; // автоматически рассчитать системой
        $rate = 1; // скорость отправки сообщений (1 = 1 смс минута). Одиночные СМС сообщения отправляются всегда с максимальной скоростью.
        $lifetime = 4; ; // срок жизни сообщения 4 часа
        $recipient = $tel;
        $user = SMS_USER; // тут ваш логин в международном формате без знака +. Пример: 380501234567
        $password = SMS_PASS; // Ваш пароль

        $myXML 	 = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $myXML 	.= "<request>"."\n";
        $myXML 	.= "<operation>SENDSMS</operation>"."\n";
        $myXML 	.= '		<message start_time="'.$start_time.'" end_time="'.$end_time.'" lifetime="'.$lifetime.'" rate="'.$rate.'" desc="'.$description.'">'."\n";
        $myXML 	.= "		<body>".$text."</body>"."\n";
        $myXML 	.= "		<recipient>".$recipient."</recipient>"."\n";
        $myXML 	.=  "</message>"."\n";
        $myXML 	.= "</request>";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERPWD , $user.':'.$password);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, 'http://sms-fly.com/api/api.noai.php');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml", "Accept: text/xml"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $myXML);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}