<?php
class ControllerAccountForgotten extends Controller {
	private $error = array();

	public function index() {
		if ($this->customer->isLogged()) {
			$this->response->redirect($this->url->link('account/account', '', true));
		}

		$this->load->language('account/forgotten');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/customer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_account_customer->editCode($this->request->post['email'], token(40));

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('account/login', '', true));
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
			'text' => $this->language->get('text_forgotten'),
			'href' => $this->url->link('account/forgotten', '', true)
		);

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['action'] = $this->url->link('account/forgotten', '', true);

		$data['back'] = $this->url->link('account/login', '', true);

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} else {
			$data['email'] = '';
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/forgotten', $data));
	}

	protected function validate() {
//		if (!isset($this->request->post['email'])) {
//			$this->error['warning'] = $this->language->get('error_email');
//		} elseif (!$this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
//			$this->error['warning'] = $this->language->get('error_email');
//		}

        if ((utf8_strlen($this->request->post['tel']) < 3) || (utf8_strlen($this->request->post['tel']) > 32)) {
            $this->error['tel'] = $this->language->get('error_telephone');
        }

        if ((utf8_strlen($this->request->post['sms_for']) < 30) || (utf8_strlen($this->request->post['sms_for']) > 32)) {
            $this->error['tel_code'] = $this->language->get('error_tel_code');
        }else{
            if(!isset($this->error['tel'])){
                if (utf8_strlen($this->request->post['tel_codes']) != 4) {
                    $this->error['tel_code'] = $this->language->get('error_tel_code_len');
                }else{
                    $code_hash = $this->request->post['sms_for'];
                    $tel = $this->request->post['tel'];
                    $tel = str_replace(' ','',$tel);
                    $tel = str_replace('+3','',$tel);

                    $code = $this->request->post['tel_codes'];
                    $hash = md5($tel.$code);
                    if($hash != $code_hash){
                        $this->error['tel_code'] = $this->language->get('error_tel_code_err');
                    }
                }
            }
        }

		// Check if customer has been approved.
		$customer_info = $this->model_account_customer->getCustomerByTel($this->request->post['tel']);

		if ($customer_info && !$customer_info['status']) {
			$this->error['warning'] = $this->language->get('error_approved');
		}

		return !$this->error;
	}

    public function forgot(){
        $data = false;

        if ($this->customer->isLogged()) {
            $this->response->redirect($this->url->link('account/account', '', true));
        }
        $this->load->language('account/register');
        $this->load->language('account/forgotten');
        $this->load->model('account/customer');

        $data['redirect'] = false;
        $data['error'] = false;
        $data['success'] = false;
        $data['text_success'] = '';

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            $cods = token(40);
            $this->model_account_customer->editCodes($this->request->post['tel'], $cods);
            //$data['text_success'] = $this->language->get('text_success');
            $this->session->data['success'] = $this->language->get('text_success');
            $data['redirect'] = $this->url->link('account/reset&code='.$cods, '', true);
            //$data['redirect'] = $this->url->link('account/login', '', true);
            $data['success'] = true;
        }else{

            if (isset($this->error['telephone'])) {
                $json['error'][] = ['inp' => "input[name='tel']" , 'text' => $this->error['telephone']];
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
                $json['error'][] = ['inp' => "input[name='tel_codes']" , 'text' => $this->error['tel_code']];
            }
//            if (isset($this->error['warning'])) {
//                $json['error'][] = ['inp' => "input[name='email']" , 'text' => $this->error['warning']];
//            }
        }




        if (!isset($json['error'])) {
            $json['success'] = $data;
        }else{
            $json['success'] = false;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function forgot_send(){
        if ($this->customer->isLogged()) {
            $this->response->redirect($this->url->link('account/account', '', true));
        }
        $this->load->language('account/register');
        $this->load->model('account/customer');

        $json = array();
        $json['redirect'] = false;
        $json['error'] = false;
        $json['success'] = false;

        if ((utf8_strlen($this->request->post['tel']) < 3) || (utf8_strlen($this->request->post['tel']) > 32)) {
            $json['error'][] = ['inp' => "input[name='tel']" , 'text' => $this->language->get('error_telephone')];
        }

        if ($this->model_account_customer->getCustomerByTel($this->request->post['tel'])) {

        }else{
            $json['error'][] = ['inp' => "input[name='tel']" , 'text' => $this->language->get('error_exists_no_tel')];
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && !$json['error']) {
            $tel = isset($this->request->post['tel'])?$this->request->post['tel']: false;
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

    private function sendSMS($tel,$text,$desc = 'send'){
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
