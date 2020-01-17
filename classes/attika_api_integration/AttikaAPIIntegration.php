<?php

class AttikaAPIIntegration
{

    public $status;
    public $msg;
    public $result;

    public $login = 'serviceapi';
    public $key = '5b303311d7eebcd1ee6fc98f205784a1bae45a509ed887e56294176a6116331ec706a2f8c26f7171bd0803e932697126a3f5fea795b16b6de6b29fda27b420a53e15c3bb9b5e45e1fa5076f2fc34a365fc12a60844c72f73274328b660a05a15d87c3356841573781f0407537c11d8e79bfcd916e0303cf516e06ca244a78c691cc8bc853306b4b4d1f6b70523c20e01ff6842cb0d5a45bb532164ecf8cb7be73804c58a538ef783456f24c76d839db6a9a13f60e06112077cd05b24c4aea197d7ac1ba928166c8ed6bd69cc7535eab4beb29b0b8fb43973f2a5fa80cf4c987700161d85ba57e55bfd3c5da754fcabf6f0e41e5a4e078c380c8098585389347b';

    public $url = "https://attika.justin.ua/api/";

    function __construct()
    {

    }

    public function getFilials()
    {
        $p['method'] = 'branches_list';
        $res = $this->sendRequest($this->getPost($p));
        return $res;
    }

    public function getStatuses()
    {
        $p['method'] = 'statuses_list';
        $res = $this->sendRequest($this->getPost($p));
        return $res;
    }

    private function sendRequest($post)
    {

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $this->url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // не проверять SSL сертификат
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // не проверять Host SSL сертификата
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        $api = curl_exec($curl);
        curl_close($curl);

        // парсим ответ
//echo $api;
        $api = json_decode($api, true);
        $this->status = $api['status'];
        $this->msg = $api['msg'];
        $this->result = $api['result'];

        return $this->result;

    }

    private function getPost($arr)
    {

        $arr['datetime'] = date('Y-m-d H:i:s');
        $arr['login'] = $this->login;
        $arr['sign'] = '';

        $post = json_encode($arr) . $this->key;
        $sign = sha1($post, true);
        $sign = bin2hex($sign);
        $arr['sign'] = $sign;

        return json_encode($arr);

    }

    public function getFilialInfo($number)
    {
        $p['method'] = 'branches_info';
        $p['searchby'] = 'number';
        $p['searchdata'] = $number;
        $res = $this->sendRequest($this->getPost($p));
        return $res;
    }

}