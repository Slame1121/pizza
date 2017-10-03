<?php
/**
 * Created by PhpStorm.
 * User: Паша
 * Date: 03.10.2017
 * Time: 11:43
 */
class ControllerAccountSmsSend extends Controller
{
    private $error = array();
    public function index()
    {
        if ($this->customer->isLogged()) {
            $this->response->redirect($this->url->link('account/account', '', true));
        }
        //$sms = $this->sendSMS('test msg sms text','test msg sms description','380994651601');
        //var_dump($sms,$this->getBallans());die;
        return true;
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