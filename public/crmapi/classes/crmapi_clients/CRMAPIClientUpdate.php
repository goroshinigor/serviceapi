<?php

class CRMAPIClientUpdate
{

    public $status;
    public $msg;

    private $result = array();

	public function __construct($data){

	    $memberId = $data['memberId'];

	    $cl = new CRMAPIClient();
        $cl->setFromDB(array('memberId'=>$memberId));
	    $client = $cl->getData();

        if($client['id'] > 0){

            if($data['lastName'] !== null) $client['lastName'] = (string)$data['lastName'];
            if($data['firstName'] !== null) $client['firstName'] = (string)$data['firstName'];
            if($data['middleName'] !== null) $client['middleName'] = (string)$data['middleName'];
            if($data['email'] !== null) $client['email'] = (string)$data['email'];
            if($data['phoneNumber'] !== null) $client['phoneNumber'] = (string)$data['phoneNumber'];
            if($data['gender'] !== null) $client['gender'] = (string)$data['gender'];
            if($data['birthday'] !== null) $client['birthday'] = (string)$data['birthday'];
            if($data['password'] !== null) $client['password'] = (string)$data['password'];

            $cl->set($client);
            if($data['password'] !== null){ // пришел новый пароль
                $cl->changeWithPass();
            } else {
                $cl->change();
            }

            $this->status = $cl->status;
            $this->msg = $cl->msg;

        } else {

            $this->status = false;
            $this->msg['code'] = 60250; // Указанный memberId не обнаружен в базе даных

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

