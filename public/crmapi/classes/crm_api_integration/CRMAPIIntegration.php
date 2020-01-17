<?php

class CRMAPIIntegration
{

    private $crm_api_url = "https://crmapi.justin.ua/api/justin/";
    private $uri;

	public function __construct(){

	}

    public function registration($data){

	    $this->uri = "Registrate";
        $p = array(
            'registrationData' => array(
                'lastName' => $data['last_name'],
                'firstName' => $data['first_name'],
                'middleName' => $data['middle_name'],
                'email' => $data['email'],
                'phoneNumber' => $data['phone']
            ),
            'password' => $data['pass']
        );

        $res = json_decode($this->sendRequest($p),true);
        print_r($res);

    }

    private function sendRequest($p){

        $post = json_encode($p);

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $this->crm_api_url.$this->uri);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // не проверять SSL сертификат
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // не проверять Host SSL сертификата
        curl_setopt($curl, CURLOPT_POST,true);
        curl_setopt($curl, CURLOPT_POSTFIELDS,$post);
        $res = curl_exec($curl);
        curl_close($curl);

        return $res;

    }


}

?>