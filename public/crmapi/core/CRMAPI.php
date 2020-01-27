<?php

class CRMAPI{

	public $status;
	public $msg;
	public $result;

	private $post_array; // Тут храним разобранный POST
	private $post_json; // Тут храним приходящие данные

	private $session_uuid; // Тут храним сессию для текущего обращения к платформе для связывания различных запросов между собой
    private $url; // адрес обращения к скрипту
	private $method; // метод - название функционала


	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function __construct(){
		$this->status = true;
		$this->session_uuid = CRMAPIWee::generateGUID();
        $this->url = $this->getUrl();
        $this->method = $this->getMethodByURI();
	}


	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function set($json){

		$this->post_json = $json; // сохраняем входящие данные в формате json
		$this->post_array = $data = json_decode($json,true); // сохраняем входящие данные в формате массива

		if((int)$data['debug_mode'] == 1){ define('DEBUG',1); } else { define('DEBUG',0); }
		if(DEBUG) echo "<pre>";

	}


	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function gogo(){

		// Заносим данные в БД что запрос поступил
		CRMAPILogRequestToAPI::addLog(array(
		    "url" => $this->url,
            "session_uuid" => $this->session_uuid,
            "post_json" => $this->post_json
        ));

		$this->post_array['session_uuid'] = $this->session_uuid; // сразу захватим в поступившие данные текущую сессию

		if($this->status){

			// Возможные варианты запросов Method с указанием на класс обработки
			$obj['Registrate'] = "CRMAPIClientRegistration";
            $obj['CheckPhone'] = "CRMAPIClientCheckPhone";
            $obj['VerifyPhone'] = "CRMAPIClientVerifyPhone";
            $obj['LoginPhoneNumber'] = "CRMAPIClientLoginPhoneNumber";

            $obj['ClientUpdate'] = "CRMAPIClientUpdate";
            $obj['ClientInfo'] = "CRMAPIClientInfo";
            $obj['ClientDelete'] = "CRMAPIClientDelete";

            $obj['SMSSend'] = "CRMAPISMSSend";


			// находим нужный класс по входящим данным
			$class = $obj[$this->method];
			if (!$class) $class = 'CRMAPI404'; // если нет такого то объявляем класс 404

			// если подпись подлинная и класс реально существует в системе, то вызываем его
			if ($this->status and class_exists($class)) {

				$a = new $class($this->post_array); // и передаем во внутрь весь массив поступивших данных

				$this->status = $a->status;
				$this->msg = $a->msg;

				// У всех классов должен быть метод getResult
				$this->result = $a->getResult();
				if (count($this->result) == 0) {
					$this->result = null;
				}
			}

		}

        CRMAPILogRequestToAPI::updateLog(array(
            "status" => $this->status,
            "msg" => $this->msg,
            "result" => $this->result,
            "session_uuid" => $this->session_uuid
        ));
		echo $this->renderResult();

	}


	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	private function renderResult(){

		if($this->msg['code'] != 0){
			require("msgs.php");
			$this->msg = array_merge($this->msg,$msg[$this->msg['code']]);
		}

		if($this->status){
            $out = array(
                'resultCode' => 1,
                'resultType' => "success",
                'resultStr' => "",
                'data' => $this->result
            );
        } else {
            $out = array(
                'resultCode' => $this->msg['code'],
                'resultType' => "error",
                'resultStr' => $this->msg['ua'],
                'data' => null
            );
        }

		header('Content-Type: application/json; charset=utf8');
		return json_encode($out,JSON_UNESCAPED_UNICODE);

	}


	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	private function getUrl(){

		$result = ''; // Пока результат пуст
		$default_port = 80; // Порт по-умолчанию

		// А не в защищенном-ли мы соединении?
		if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=='on')) {
			// В защищенном! Добавим протокол...
			$result .= 'https://';
			// ...и переназначим значение порта по-умолчанию
			$default_port = 443;
		} else {
			// Обычное соединение, обычный протокол
			$result .= 'http://';
		}
		// Имя сервера, напр. site.com или www.site.com
		$result .= $_SERVER['SERVER_NAME'];

		// А порт у нас по-умолчанию?
		if ($_SERVER['SERVER_PORT'] != $default_port) {
			// Если нет, то добавим порт в URL
			$result .= ':'.$_SERVER['SERVER_PORT'];
		}
		// Последняя часть запроса (путь и GET-параметры).
		$result .= $_SERVER['REQUEST_URI'];

		return $result;

	}


    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    private function getMethodByURI(){

        // Последняя часть запроса (путь и GET-параметры).
        $URI = $_SERVER['REQUEST_URI'];
        $temp = explode("/",$URI);

        foreach($temp as $v){
            if(!empty($v)) $method = $v;
        }

        return $method;

    }


}

?>