<?php

class CRMAPIClientCheckPhone
{

    public $status;
    public $msg;

    private $result = array();

	public function __construct($data){

        $phoneNumber = (string)$data['phoneNumber'];

        $res = CRMAPIDB::prepare("SELECT * FROM crmapi_clients WHERE phoneNumber = :phoneNumber ");
        $res->bindParam("phoneNumber",$phoneNumber);
        $res->execute();
        $r = $res->fetch(PDO::FETCH_ASSOC);
        if($r['id'] > 0) {

            $this->status = true;
            $this->setResult(array('memberId'=>$r['memberId']));

        } else {

            $this->status = false;
            $this->msg['code'] = 60230;

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

