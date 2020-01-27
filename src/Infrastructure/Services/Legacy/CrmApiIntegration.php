<?php

namespace App\Infrastructure\Services\Legacy;

class CrmApiIntegration
{

    private $crm_api_url = "https://crmapi.justin.ua/api/justin/";
    private $uri;

    public function __construct()
    {

    }

    public function registration($data)
    {

        $this->uri = "Registrate";
        $p = array(
            'registrationData' => array(
                'lastName' => $data['last_name'],
                'firstName' => $data['first_name'],
                'middleName' => $data['middle_name'],
                'email' => $data['email'],
                'phoneNumber' => $data['phone'],
                'gender' => $data['gender'],
                'birthday' => $data['birthday']
            ),
            'password' => $data['pass']
        );

        $res = json_decode($this->sendRequest($p), true);

        return $res;

    }

    private function sendRequest($p)
    {

        $post = json_encode($p);

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $this->crm_api_url . $this->uri);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        $res = curl_exec($curl);
        curl_close($curl);

        return $res;

    }

    public function clientUpdate($data)
    {

        $this->uri = "ClientUpdate";
        $p = array(
            'memberId' => $data['memberId'],
            'lastName' => $data['last_name'],
            'firstName' => $data['first_name'],
            'middleName' => $data['middle_name'],
            'email' => $data['email'],
            'phoneNumber' => $data['phone'],
            'password' => $data['pass'],
            'gender' => $data['gender'],
            'birthday' => $data['birthday']
        );

        $res = json_decode($this->sendRequest($p), true);

        return $res;

    }

    public function clientDelete($data)
    {

        $this->uri = "ClientDelete";
        $p = array(
            'memberId' => $data['memberId']
        );

        $res = json_decode($this->sendRequest($p), true);

        return $res;

    }

    public function verifyPhone($data)
    {

        $this->uri = "VerifyPhone";
        $p = array(
            'phoneNumber' => $data['phone']
        );

        $res = json_decode($this->sendRequest($p), true);

        return $res;

    }

    public function checkPhone($data)
    {
        $this->uri = "CheckPhone";
        $p = array(
            'phoneNumber' => $data
        );

        $res = json_decode($this->sendRequest($p), true);

        return $res;

    }

    public function loginPhone($data)
    {

        $this->uri = "LoginPhoneNumber";
        $p = array(
            'phoneNumber' => $data['phone'],
            'password' => $data['pass']
        );

        $res = json_decode($this->sendRequest($p), true);
        if ($res['resultCode']) {

            $res['data']['last_name'] = $res['data']['lastName'];
            $res['data']['first_name'] = $res['data']['firstName'];
            $res['data']['middle_name'] = $res['data']['middleName'];
            $res['data']['phone'] = $res['data']['phoneNumber'];

            unset($res['data']['lastName']);
            unset($res['data']['firstName']);
            unset($res['data']['middleName']);
            unset($res['data']['phoneNumber']);
        }

        return $res;

    }

    public function clientInfo($data)
    {

        $this->uri = "ClientInfo";
        $p = array(
            'memberId' => $data['memberId']
        );

        $res = json_decode($this->sendRequest($p), true);
        if ($res['resultCode']) {

            $res['data']['last_name'] = $res['data']['lastName'];
            $res['data']['first_name'] = $res['data']['firstName'];
            $res['data']['middle_name'] = $res['data']['middleName'];
            $res['data']['phone'] = $res['data']['phoneNumber'];

            unset($res['data']['lastName']);
            unset($res['data']['firstName']);
            unset($res['data']['middleName']);
            unset($res['data']['phoneNumber']);
        }

        return $res;

    }

    public function SMSSend($data)
    {

        $this->uri = "SMSSend";
        $p = array(
            'phoneNumber' => $data['phone'],
            'text' => $data['text'],
            'convertToTranslit' => $data['convert_to_translit']
        );

        $res = json_decode($this->sendRequest($p), true);

        return $res;

    }


}
