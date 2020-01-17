<?php

class ServiceAPIClientsRegistration
{

    public $status;
    public $msg;

    private $result;

    public function __construct($data){

        $this->status = true;
        $d = (array)$data['data']; // набор данных клиента
        $d['api_user_id'] = (string)$data['api_user_id']; // встраиваем в данные пользователя
        $d['session_uuid'] = (string)$data['session_uuid']; // сессию

        if($this->status){
            $a = new CRMAPIIntegration();
            $a->registration($d);

            $this->status = $a->status;
            $this->msg = $a->msg;
        }

        if($this->status){
            $data = $a->getData();
            $this->setResult($data);
        }

    }


    private function setResult($data){
        $this->result = $data;
        return $this->result;
    }


    public function getResult(){
        return $this->result;
    }

}

?>