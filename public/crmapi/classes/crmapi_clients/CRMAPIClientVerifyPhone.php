<?php

class CRMAPIClientVerifyPhone
{

    public $status;
    public $msg;

    private $result = array();

	public function __construct($data){

        $phoneNumber = (string)$data['phoneNumber'];

        $code = rand(10000,99999);

        $sms = new CRMAPITurboSMS();
        $res = $sms->sendSMS(array('phone'=>$phoneNumber,'text'=>$code));

        $res = json_decode($res,true); 

        if($res['msg'] == 1) {

            $this->status = true;
            $this->setResult(array('verify_code'=>$code));

        } elseif($res['msg'] == 2) {

            $this->status = false;
            $this->msg['code'] = 60200;

        } elseif($res['msg'] == 3) {

            $this->status = false;
            $this->msg['code'] = 60201;

        }

//    $res = CRMAPIDB::query("SELECT * FROM crmapi_clients WHERE phoneNumber = '".$phoneNumber."' "); $r = $res->fetch(PDO::FETCH_ASSOC);
//    if($r['id'] > 0){


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

