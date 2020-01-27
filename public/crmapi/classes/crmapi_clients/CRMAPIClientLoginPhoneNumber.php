<?php

class CRMAPIClientLoginPhoneNumber
{

    public $status;
    public $msg;

    private $result = array();

	public function __construct($data){

        $phoneNumber = (string)$data['phoneNumber'];
        $password = (string)$data['password'];
        $res = CRMAPIDB::prepare(" SELECT * FROM crmapi_clients WHERE phoneNumber = :phoneNumber ");
        $res->bindParam('phoneNumber',$phoneNumber );
        $res->execute();
        $r = $res->fetch(PDO::FETCH_ASSOC);
        if($r['id'] > 0){
            if(password_verify($password,$r['password'])) {
                $cl = new CRMAPIClient(); $cl->setFromDB(array('id'=>$r['id'])); $client = $cl->getDataPublic();
                $this->status = true;
                $this->setResult($client);
            } else {
                $this->status = false;
                $this->msg['code'] = 60211;
            }
        } else {
            $this->status = false;
            $this->msg['code'] = 60212;
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

