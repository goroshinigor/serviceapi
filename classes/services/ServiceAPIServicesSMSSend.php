<?php

class ServiceAPIServicesSMSSend
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
            $a = new CRMAPIIntegration();
            $res = $a->SMSSend($d);

            if ($res['resultCode'] == 1) {
                $this->status = true;
                $this->setResult($res['data']);
            } else {
                $this->status = false;
                $this->msg['code'] = $res['resultCode'];
            }
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
