<?php

class CRMAPITurboSMS
{

	public $status;
	public $msg;

	private $url = "http://turbosms.in.ua/api/wsdl.html";
	private $login = "Justin";
	private $pass = 'Justin$$$T00mnic';
	private $sender = "Justin";

	private $client;
	private $balance;

	public function __construct(){

		$this->client = new SoapClient($this->url);
		//print_r($this->client->__getFunctions());

		$auth = [
			'login' => $this->login,
			'password' => $this->pass
		];
		$this->client->Auth($auth);

	}

	public function getBalance(){
		$result = $this->client->GetCreditBalance();
		$this->balance = $result->GetCreditBalanceResult;

		return $this->balance;
	}

	public function sendSMS($data){

		$text = (string)$data['text'];
		$phone = (string)$data['phone'];

		if(strlen($text) > 0){
			if(preg_match("/^\+380[0-9]{9}$/",$phone)){
				$sms = [
					'sender' => $this->sender,
					'destination' => $phone,
					'text' => $text
				];
				$result = $this->client->SendSMS($sms);
				$msg = $result->SendSMSResult->ResultArray[0];
				if($msg == 'Сообщения успешно отправлены'){
					$this->status = true;
                    $this->msg = 1; // Сообщения успешно отправлены
				} else {
					$this->status = false;
                    $this->msg = 2; // Сообщения не отправлено
				}
			} else {
				$this->status = false;
				$this->msg = 3; // "Не корректно указан номер телефона";
			}
		} else {
			$this->status = false;
			$this->msg = 4; // "Не указан текст сообщения";
		}

		return json_encode(array('status'=>$this->status,'msg'=>$this->msg));

	}

}

?>