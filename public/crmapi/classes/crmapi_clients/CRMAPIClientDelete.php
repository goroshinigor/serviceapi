<?php

class CRMAPIClientDelete
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

            $cl->delete();

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

