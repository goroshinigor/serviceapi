<?php

class ServiceAPIJustinApiPMS
{

    public $auth_login = 'Exchange';
    public $auth_password = 'Exchange';

    public $client_login = '';
    public $client_password = '';

    public $language = 'UA';

    public $url = "http://api.justin.ua/justin_pms/hs/v2/runRequest";

    public $test_mode = "not_test";

    function __construct($client_login = 'BigData', $client_password = 'eeFTxCQV', $language = 'UA', $test_mode = 'not_test')
    {
        $this->client_login = $client_login;
        $this->client_password = $client_password;
        $this->language = $language;
        if ($test_mode != 'not_test') {
            $this->url = "http://api.justin.ua/justin_pms_test/hs/v2/runRequest";
            $this->test_mode = $test_mode;
        }

        return $this;
    }

    // Отримання даних відділень
    public function get_departments($filter = array())
    {

        $json_value = array(
            "keyAccount" => $this->client_login,
            "sign" => $this->forming_sign(),
            "request" => 'getData',
            "type" => 'request',
            "name" => 'req_DepartmentsLang',
            "language" => $this->language,
            "params" => array(
                "language" => $this->language
            )
        );

        if ($filter != array()) {
            $json_value['filter'] = $filter;
        }

        $return = $this->send_request($json_value);

        if ($return['response']['status'] == 1) {
            return $return['data'];
        } else { // Якщо у відповідь отримали помилку - повертаємо її
            return $return['response']['message'];
        }
    }

    // Отримання типів відділень
    public function get_department_types()
    {

        $json_value = array(
            "keyAccount" => $this->client_login,
            "sign" => $this->forming_sign(),
            "request" => 'getData',
            "type" => 'catalog',
            "name" => 'cat_branchType'
        );

        $return = $this->send_request($json_value);

        if ($return['response']['status'] == 1) {
            return $return['data'];
        } else { // Якщо у відповідь отримали помилку - повертаємо її
            return $return['response']['message'];
        }
    }

    // Отримання даних вулиць
    public function get_streets($filter = array())
    {

        $json_value = array(
            "keyAccount" => $this->client_login,
            "sign" => $this->forming_sign(),
            "request" => 'getData',
            "type" => 'catalog',
            "name" => 'cat_cityStreets',
            "language" => $this->language
        );

        if ($filter != array()) {
            $json_value['filter'] = $filter;
        }

        $return = $this->send_request($json_value);

        if ($return['response']['status'] == 1) {
            return $return['data'];
        } else { // Якщо у відповідь отримали помилку - повертаємо її
            return $return['response']['message'];
        }
    }

    // Отримання даних районів міст
    public function get_city_regions($filter = array())
    {

        $json_value = array(
            "keyAccount" => $this->client_login,
            "sign" => $this->forming_sign(),
            "request" => 'getData',
            "type" => 'catalog',
            "name" => 'cat_cityRegions',
            "language" => $this->language
        );

        if ($filter != array()) {
            $json_value['filter'] = $filter;
        }

        $return = $this->send_request($json_value);

        if ($return['response']['status'] == 1) {
            return $return['data'];
        } else { // Якщо у відповідь отримали помилку - повертаємо її
            return $return['response']['message'];
        }
    }

    // Отримання даних населених пунктів
    public function get_cities($filter = array())
    {

        $json_value = array(
            "keyAccount" => $this->client_login,
            "sign" => $this->forming_sign(),
            "request" => 'getData',
            "type" => 'catalog',
            "name" => 'cat_Cities',
            "language" => $this->language
        );

        if ($filter != array()) {
            $json_value['filter'] = $filter;
        }

        $return = $this->send_request($json_value);

        if ($return['response']['status'] == 1) {
            return $return['data'];
        } else { // Якщо у відповідь отримали помилку - повертаємо її
            return $return['response']['message'];
        }
    }

    // Отримання даних обласних районів
    public function get_region_areas($filter = array())
    {

        $json_value = array(
            "keyAccount" => $this->client_login,
            "sign" => $this->forming_sign(),
            "request" => 'getData',
            "type" => 'catalog',
            "name" => 'cat_areasRegion',
            "language" => $this->language
        );

        if ($filter != array()) {
            $json_value['filter'] = $filter;
        }

        $return = $this->send_request($json_value);

        if ($return['response']['status'] == 1) {
            return $return['data'];
        } else { // Якщо у відповідь отримали помилку - повертаємо її
            return $return['response']['message'];
        }
    }

    // Отримання даних областей
    public function get_regions($filter = array())
    {

        $json_value = array(
            "keyAccount" => $this->client_login,
            "sign" => $this->forming_sign(),
            "request" => 'getData',
            "type" => 'catalog',
            "name" => 'cat_Region',
            "language" => $this->language
        );

        if ($filter != array()) {
            $json_value['filter'] = $filter;
        }

        $return = $this->send_request($json_value);

        if ($return['response']['status'] == 1) {
            return $return['data'];
        } else { // Якщо у відповідь отримали помилку - повертаємо її
            return $return['response']['message'];
        }
    }

    // Отримання UUID
    public function get_uuid()
    {

        $json_value = array(
            "keyAccount" => $this->client_login,
            "sign" => $this->forming_sign(),
            "request" => 'getData',
            "type" => 'infoData',
            "name" => 'getSenderUUID',
            "filter" => array(
                0 => array(

                    "name" => 'login',
                    "comparison" => 'equal',
                    "leftValue" => $this->client_login
                )
            )
        );

        $return = $this->send_request($json_value);

        if ($return['response']['status'] == 1) {
            return $return['data'];
        } else { // Якщо у відповідь отримали помилку - повертаємо її
            return $return['response']['message'];
        }
    }

    // Отримання переліку всіх доступних статусів
    public function get_statuses_list()
    {

        $json_value = array(
            "keyAccount" => $this->client_login,
            "sign" => $this->forming_sign(),
            "request" => 'getData',
            "type" => 'catalog',
            "name" => 'orderStatuses',
            "filter" => array()
        );

        $return = $this->send_request($json_value);

        if ($return['response']['status'] == 1) {
            return $return['data'];
        } else { // Якщо у відповідь отримали помилку - повертаємо її
            return $return['response']['message'];
        }
    }

    // Запит доставки
    public function request_delivery($api_key, $data)
    {

        $json_value = array(
            "api_key" => $api_key,
            "data" => $data
        );

        if ($this->test_mode != 'not_test') {
            $return = $this->send_request($json_value, '195.201.72.186/api_pms_demo/hs/api/v1/documents/orders');
        } else {
            $return = $this->send_request($json_value, '195.201.72.186/api_pms/hs/api/v1/documents/orders');
        }

        // Якщо у відповідь отримали помилки - повертаємо їх
        if ($return['result'] == 'error') {
            return $return['errors'];
        } else {
            return $return['data'];
        }
    }

    // Отримання інформації за ЕН по номеру телефону
    public function get_orders_info($api_key, $filter, $page = 1, $page_amount = 100, $test_mode = 'not_test')
    {

        $json_value = array(
            'api_key' => $api_key,
            'page' => $page,
            'page_amount' => $page_amount,
            'filter' => $filter
        );

        if ($test_mode != 'not_test') {
//            $return = $this->send_request($json_value, "https://api.justin.ua/justin_pms_test/hs/api/v1/documents/ordersInfo");
            $return = $this->send_request($json_value, "http://api.justin.ua/api_justin_test/hs/api/v1/documents/ordersInfo");
        } else {
            $return = $this->send_request($json_value, "https://api.justin.ua/justin_pms/hs/api/v1/documents/ordersInfo");
        }

        // Якщо у відповідь отримали помилки - повертаємо їх
        if ($return['result'] == 'error') {
            return $return['errors'];
        } else {
            return $return;
        }
    }

    // Отримання повної інформації по одній ЕН
    public function get_order_info_all($api_key, $order_number, $test_mode = 'not_test')
    {

        $json_value = array(
            'api_key' => $api_key,
            'order_number' => $order_number
        );

        if ($test_mode != 'not_test') {
            $return = $this->send_request($json_value, "https://api.justin.ua/justin_pms_test/hs/api/v1/documents/getOrderInfo");
        } else {
            $return = $this->send_request($json_value, "https://api.justin.ua/justin_pms/hs/api/v1/documents/getOrderInfo");
        }

        // Якщо у відповідь отримали помилки - повертаємо їх
        if ($return['result'] == 'error') {
            return $return['errors'];
        } else {
            return $return;
        }
    }

    // Відправлення запитів
    private function send_request($json_value, $standart_url = 1)
    {

        // Якщо функція отримала URL - беремо його
        if ($standart_url == 1) {
            $url = $this->url;
        } else {
            $url = $standart_url;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json_value));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json', "Authorization: Basic " . base64_encode("$this->auth_login:$this->auth_password")));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        $output = curl_exec($ch);
        curl_close($ch);

//        // Записуємо логи
//        $log = new BDLogRequestToAPI();
//        $log->addLog('JustIn_PMS', json_encode($json_value), $output);

        return json_decode($output, true);
    }

    // Отримання статусів
    public function get_statuses($filter = array())
    {

        $json_value = array(
            "keyAccount" => $this->client_login,
            "sign" => $this->forming_sign(),
            "request" => 'getData',
            "type" => 'request',
            "name" => 'getOrderStatusesHistoryF',
            'TOP' => 100,
        );

        if ($filter != array()) {
            $json_value['filter'] = $filter;
        }

        $return = $this->send_request($json_value);

        if ($return['response']['status'] == 1) {
            return $return['data'];
        } else { // Якщо у відповідь отримали помилку - повертаємо її
            return $return['response']['message'];
        }
    }

    // Отримання статусів (застарілий метод (внутрішній))
    public function get_statuses_old($filter = array())
    {

        $json_value = array(
            "keyAccount" => $this->client_login,
            "sign" => $this->forming_sign(),
            "request" => 'getData',
            "type" => 'request',
            "name" => 'getOrderStatusesHistory',
            'TOP' => 20,
        );

        if ($filter != array()) {
            $json_value['filter'] = $filter;
        }

        $return = $this->send_request($json_value);

        if ($return['response']['status'] == 1) {
            return $return['data'];
        } else { // Якщо у відповідь отримали помилку - повертаємо її
            return $return['response']['message'];
        }
    }

    // Формування підпису
    private function forming_sign()
    {
        return sha1($this->client_password . ':' . date('Y-m-d'));
    }


}