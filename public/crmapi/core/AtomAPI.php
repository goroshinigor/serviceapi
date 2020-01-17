<?php

class AtomAPI{

	public $status;
	public $msg;
	public $result;

	private $post_array; // Тут храним разобранный POST
	private $post_json; // Тут храним приходящие данные

	private $session_uuid; // Тут храним сессию для текущего обращения к платформе для связывания различных запросов между собой
    private $url; // адрес обращения к скрипту

    // входящие данные
	private $method; // метод - название функционала
	private $filters; // фильтры отображения выводимой информации функционала
	private $data; // массив информация для внесения
	private $format; // формат вывода информации JSON / XML
    private $output; // сегментирование вывода информации
    private $page; // номер страницы вывода
	private $login;
	private $sign; // подпись
	private $datetime; // дата и время запроса


	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function __construct(){
		$this->status = true;
		$this->session_uuid = AtomWee::generateGUID();
        $this->url = $this->getUrl();
	}


	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function set($json){

		$this->post_json = $json; // сохраняем входящие данные в формате json
		$this->post_array = $data = json_decode($json,true); // сохраняем входящие данные в формате массива

		$this->method = (string)$data['method'];
        $this->filters = (array)$data['filters'];
        $this->data = (array)$data['data'];
		$this->format = (string)$data['format'];
        $this->output = (string)$data['output'];
        $this->page = (int)$data['page'];
		$this->login = (string)$data['login'];
		$this->sign = (string)$data['sign'];
		$this->datetime = (string)$data['datetime'];

		if((int)$data['debug_mode'] == 1){ define('DEBUG',1); } else { define('DEBUG',0); }
		if(DEBUG) echo "<pre>";

	}


	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function gogo(){

		// Заносим данные в БД что запрос поступил
		AtomLogRequestToAPI::addLog(array(
		    "url" => $this->url,
            "login" => $this->login,
            "session_uuid" => $this->session_uuid,
            "post_json" => $this->post_json
        ));

		// находим пользователя по логину
		$a = new AtomAPIUsers(); $a->setFromDBByLogin($this->login); $b = $a->getData();

		if($b['id'] > 0){
			$this->post_array['api_user_id'] = $b['id'];
			$this->post_array['master_system'] = $b['master_system'];
			$this->post_array['session_uuid'] = $this->session_uuid; // сразу захватим в поступившие данные текущую сессию
		} else {
			$this->status = false;
			$this->msg['code'] = 50004; // Пользователь не обнаружен в Базе
		}

		if($this->status){

			// Возможные варианты запросов Method с указанием на класс обработки
			$obj['client_registration'] = "ServiceAPIClientsRegistration";


			// находим нужный класс по входящим данным
			$class = $obj[strtolower($this->method)];
			if (!$class) $class = 'AtomAPI404'; // если нет такого то объявляем класс 404

			// если класс существует - не 404
			if ($class != "AtomAPI404") {
				$a = new AtomAPIAccess($this->post_json); // проверяем подпись
				$this->status = $a->status;
				$this->msg = $a->msg;
			}

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

        AtomLogRequestToAPI::updateLog(array(
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

		$out = array(
			'status' => $this->status?1:0,
			'msg' => $this->msg,
			'result' => $this->result
		);

		if($this->format != 'xml'){
			header('Content-Type: application/json; charset=utf8');
			return json_encode($out,JSON_UNESCAPED_UNICODE);
		} else {
			header('Content-Type: text/xml; charset=utf8');
			return $this->toXML($out);
		}

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
	private function toXML($data){
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= "<atomapiplatform>";
		$xml .= $this->toXMLObj($data);
		$xml .= "</atomapiplatform>";

		return $xml;
	}


	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	private function toXMLObj($data){
		$xml = '';
		foreach((array)$data as $k=>$v){
			if(!is_numeric($k)){
				if(is_null($v)){
					$xml .= "<".$k." />";
				} else {
					$xml .= "<".$k.">";
					if(is_array($v)){
						$xml .= $this->toXMLObj($v);
					} else {
						$xml .= $v;
					}
					$xml .= "</".$k.">";
				}
			}
		}
		return $xml;
	}



}

?>