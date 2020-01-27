<?php

class CRMAPILogRequestToAPI
{

	public function __construct(){

	}

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Создание записи в истории
    // Если неудача, то работа АПИ не должна останавливаться
    // Тоесть ошибки в формированиии Логов не критичны

    public static function addLog($data){

        $headers_string = '';

        // формируем пулл данных по заголовку
        if(!function_exists('getallheaders')){
            $headers = array();
            foreach ($_SERVER as $name => $value){
                if(substr($name, 0, 5) == 'HTTP_'){
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
        } else {
            $headers = getallheaders();
        }

        foreach($headers as $name => $value){ $headers_string .= "$name: $value\n"; }

        // формируем запись для БД
        $sql = "
				INSERT INTO crmapii_log_requests_to_api SET
				login = :login,
				session_uuid = :session_uuid,
				request_url = :request_url,
				request_data = :request_data,
				request_headers = :request_headers,
				request_datetime = :request_datetime,
				request_remote_ip = :request_remote_ip
			";
        $res = CRMAPIDB::r()->prepare($sql);
        $res->bindValue('login',$data['login']);
        $res->bindValue('session_uuid',$data['session_uuid']);
        $res->bindValue('request_url',$data['url']);
        $res->bindValue('request_data',$data['post_json']);
        $res->bindValue('request_headers',$headers_string);
        $res->bindValue('request_datetime',date('Y-m-d H:i:s'));
        $res->bindValue('request_remote_ip',$_SERVER['REMOTE_ADDR']);
        $ok = $res->execute();

        if(!$ok){
            return false;
        }

        return true;
    }


    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function updateLog($data){

        $status = 0;
        if($data['status']) $status = 1;

        $sql = "
				UPDATE crmapii_log_requests_to_api SET
				status = :status,
				msg_code = :msg_code,
				result_data = :result_data,
				result_datetime = :result_datetime
				where session_uuid = :session_uuid
			";
        $res = CRMAPIDB::r()->prepare($sql);
        $res->bindValue('status',$status);
        $res->bindValue('msg_code',$data['msg']['code']);
        $res->bindValue('result_data',json_encode($data['result']));
        $res->bindValue('result_datetime',date('Y-m-d H:i:s'));
        $res->bindValue('session_uuid',$data['session_uuid']);
        $ok = $res->execute();

        if($ok){
            return true;
        } else {
            return false;
        }

    }



}

?>