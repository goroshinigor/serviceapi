<?php

namespace App\Infrastructure\Services\Remote\CrmApiIntegration\Client;

class GetInfoService {

    /**
     *
     * @var type 
     */
    private $crmApiUrl = "https://crmapi.justin.ua/api/justin/";

    /**
     * 
     */
    public function get(string $memberId) : array
    {

        $this->uri = "ClientInfo";
        $p = array(
            'memberId' => $memberId
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

        return $res['data'];
    }

    /**
     * 
     * @param type $p
     * @return type
     */
    private function sendRequest($p)
    {
        $post = json_encode($p);

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $this->crmApiUrl . $this->uri);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // не проверять SSL сертификат
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // не проверять Host SSL сертификата
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }
}
