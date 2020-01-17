<?php

class CRMAPIClientRegistration
{

    public $status;
    public $msg;

    private $result = array();

	public function __construct($data){

        $toDB['memberType'] = 'person';
	    $toDB['lastName'] = (string)$data['registrationData']['lastName'];
        $toDB['firstName'] = (string)$data['registrationData']['firstName'];
        $toDB['middleName'] = (string)$data['registrationData']['middleName'];
        $toDB['email'] = (string)$data['registrationData']['email'];
        $toDB['phoneNumber'] = (string)$data['registrationData']['phoneNumber'];
        $toDB['password'] = (string)$data['password'];
        $toDB['gender'] = (int)$data['registrationData']['gender'];
        $toDB['birthday'] = (string)$data['registrationData']['birthday'];

        $a = new CRMAPIClient();
        $a->set($toDB);
        $memberId = $a->add();

        $this->status = $a->status;
        $this->msg = $a->msg;

        if($this->status) $this->setResult(array('memberId'=>$memberId));

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

