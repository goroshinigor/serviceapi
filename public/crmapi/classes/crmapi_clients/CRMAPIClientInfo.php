<?php

class CRMAPIClientInfo
{

    public $status;
    public $msg;

    private $result = array();

	public function __construct($data){

        $memberId = (string)$data['memberId'];

        $cl = new CRMAPIClient(); $cl->setFromDB(array('memberId'=>$memberId)); $client = $cl->getDataPublic();

        if($client['memberId'] != ""){
            $this->status = true;
            $this->setResult($client);
        } else {
            $this->status = false;
            $this->msg['code'] = 60101;
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

