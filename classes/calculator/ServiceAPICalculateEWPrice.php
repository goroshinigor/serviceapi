<?php

class ServiceAPICalculateEWPrice
{

    public $status;
    public $msg;

    private $result;

    public function __construct($data)
    {

        $this->status = true;
        $d = (array)$data['data']; // набор данных клиента
        $d['api_user_id'] = (string)$data['api_user_id']; // встраиваем в данные пользователя
        $d['session_uuid'] = (string)$data['session_uuid']; // сессию

        if ($this->status) {

            $a = new ServiceAPICalculator($d);
            $this->setResult($a->getResult());

            $this->status = $a->status;
            $this->msg = $a->msg;

        }

    }


    private function setResult($data)
    {
        $this->result = $data;
        return $this->result;
    }


    public function getResult()
    {
        return $this->result;
    }

}