<?php

//ini_set('display_errors',1);
//error_reporting(E_ERROR+E_COMPILE_ERROR+E_CORE_ERROR+E_PARSE+E_USER_ERROR);

$key = '';
$p = array();

//hi

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Зарегестрировать Клиента

//$p['method'] = 'client_registration';
//$p['data'] = array(
//	'last_name' => 'Кукуруза3',
//	'first_name' => 'Константин3',
//	'middle_name' => 'Васильович',
//	'phone' => '+380979999348',
//	'email' => 'bibi@justin.ua',
//    'pass' => '171717',
//);
//$p['login'] = 'test';
//$p['datetime'] = date('Y-m-d H:i:s');
//$p['sign'] = '';
//$key = "7ac6afc5d17f8e5fe37a568288202574d7dc5ed2f973e7f7f4132a3419e9d766b5b8ea2d73d259680a30ab877edfcc045980d6f8c890456a8bdbc23cbebb344110af2ae9fd389fa47a84f2c5e014e43ac79b4ec89d89f74ce49e945574c355a664d12f8b5b6946ed44dc2b0a76416377a2e6f98a2379b210504c730255707624adf78633f427c207fc49e872ff7f652bdc0c28870cfc9126c0a8902c6a269edbed429276cbc98099459fb3b34825f5388802b37f650f611592c49ab49281f44e8c0100472602a8510447f595110cbeec59e8f1d3107d0bffcfb16d231b0164b37ad07002c440ac5510e0df621f1dfbe130199235806f13caf4e142492dff1719";

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Изменить Клиента

//$p['method'] = 'client_update';
//$p['data'] = array(
//    'memberId' => '000000017',
//	'last_name' => 'Кукуруза',
//	'first_name' => 'Константин',
//	'middle_name' => 'Васильович',
//	'phone' => '+380979999999',
//	'email' => 'bibi@justin.ua',
//    'birthday' => '',
//    'pass' => '12345',
//);
//$p['login'] = 'test';
//$p['datetime'] = date('Y-m-d H:i:s');
//$p['sign'] = '';
//$key = "7ac6afc5d17f8e5fe37a568288202574d7dc5ed2f973e7f7f4132a3419e9d766b5b8ea2d73d259680a30ab877edfcc045980d6f8c890456a8bdbc23cbebb344110af2ae9fd389fa47a84f2c5e014e43ac79b4ec89d89f74ce49e945574c355a664d12f8b5b6946ed44dc2b0a76416377a2e6f98a2379b210504c730255707624adf78633f427c207fc49e872ff7f652bdc0c28870cfc9126c0a8902c6a269edbed429276cbc98099459fb3b34825f5388802b37f650f611592c49ab49281f44e8c0100472602a8510447f595110cbeec59e8f1d3107d0bffcfb16d231b0164b37ad07002c440ac5510e0df621f1dfbe130199235806f13caf4e142492dff1719";

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Удалить Клиента

//$p['method'] = 'client_delete';
//$p['data'] = array(
//    'memberId' => '000000070'
//);
//$p['login'] = 'test';
//$p['datetime'] = date('Y-m-d H:i:s');
//$p['sign'] = '';
//$key = "7ac6afc5d17f8e5fe37a568288202574d7dc5ed2f973e7f7f4132a3419e9d766b5b8ea2d73d259680a30ab877edfcc045980d6f8c890456a8bdbc23cbebb344110af2ae9fd389fa47a84f2c5e014e43ac79b4ec89d89f74ce49e945574c355a664d12f8b5b6946ed44dc2b0a76416377a2e6f98a2379b210504c730255707624adf78633f427c207fc49e872ff7f652bdc0c28870cfc9126c0a8902c6a269edbed429276cbc98099459fb3b34825f5388802b37f650f611592c49ab49281f44e8c0100472602a8510447f595110cbeec59e8f1d3107d0bffcfb16d231b0164b37ad07002c440ac5510e0df621f1dfbe130199235806f13caf4e142492dff1719";

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Инфо Клиента

//$p['method'] = 'client_info';
//$p['data'] = array(
//    'memberId' => '000000017',
//);
//$p['login'] = 'test';
//$p['datetime'] = date('Y-m-d H:i:s');
//$p['sign'] = '';
//$key = "7ac6afc5d17f8e5fe37a568288202574d7dc5ed2f973e7f7f4132a3419e9d766b5b8ea2d73d259680a30ab877edfcc045980d6f8c890456a8bdbc23cbebb344110af2ae9fd389fa47a84f2c5e014e43ac79b4ec89d89f74ce49e945574c355a664d12f8b5b6946ed44dc2b0a76416377a2e6f98a2379b210504c730255707624adf78633f427c207fc49e872ff7f652bdc0c28870cfc9126c0a8902c6a269edbed429276cbc98099459fb3b34825f5388802b37f650f611592c49ab49281f44e8c0100472602a8510447f595110cbeec59e8f1d3107d0bffcfb16d231b0164b37ad07002c440ac5510e0df621f1dfbe130199235806f13caf4e142492dff1719";


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Проверка телефона Клиента

//$p['method'] = 'client_verify_phone';
//$p['data'] = array(
//    'phone' => '+380972706986',
//);
//$p['login'] = 'test';
//$p['datetime'] = date('Y-m-d H:i:s');
//$p['sign'] = '';
//$key = "7ac6afc5d17f8e5fe37a568288202574d7dc5ed2f973e7f7f4132a3419e9d766b5b8ea2d73d259680a30ab877edfcc045980d6f8c890456a8bdbc23cbebb344110af2ae9fd389fa47a84f2c5e014e43ac79b4ec89d89f74ce49e945574c355a664d12f8b5b6946ed44dc2b0a76416377a2e6f98a2379b210504c730255707624adf78633f427c207fc49e872ff7f652bdc0c28870cfc9126c0a8902c6a269edbed429276cbc98099459fb3b34825f5388802b37f650f611592c49ab49281f44e8c0100472602a8510447f595110cbeec59e8f1d3107d0bffcfb16d231b0164b37ad07002c440ac5510e0df621f1dfbe130199235806f13caf4e142492dff1719";

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Проверка телефона Клиента

//$p['method'] = 'client_check_phone';
//$p['data'] = array(
//    'phone' => '+380972706986',
//);
//$p['login'] = 'test';
//$p['datetime'] = date('Y-m-d H:i:s');
//$p['sign'] = '';
//$key = "7ac6afc5d17f8e5fe37a568288202574d7dc5ed2f973e7f7f4132a3419e9d766b5b8ea2d73d259680a30ab877edfcc045980d6f8c890456a8bdbc23cbebb344110af2ae9fd389fa47a84f2c5e014e43ac79b4ec89d89f74ce49e945574c355a664d12f8b5b6946ed44dc2b0a76416377a2e6f98a2379b210504c730255707624adf78633f427c207fc49e872ff7f652bdc0c28870cfc9126c0a8902c6a269edbed429276cbc98099459fb3b34825f5388802b37f650f611592c49ab49281f44e8c0100472602a8510447f595110cbeec59e8f1d3107d0bffcfb16d231b0164b37ad07002c440ac5510e0df621f1dfbe130199235806f13caf4e142492dff1719";

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//// Авторизация Клиента по телефону

//$p['method'] = 'client_login_phone';
//$p['data'] = array(
//    'phone' => '+380979999999',
//    'pass' => '12345',
//);
//$p['login'] = 'test';
//$p['datetime'] = date('Y-m-d H:i:s');
//$p['sign'] = '';
//$key = "7ac6afc5d17f8e5fe37a568288202574d7dc5ed2f973e7f7f4132a3419e9d766b5b8ea2d73d259680a30ab877edfcc045980d6f8c890456a8bdbc23cbebb344110af2ae9fd389fa47a84f2c5e014e43ac79b4ec89d89f74ce49e945574c355a664d12f8b5b6946ed44dc2b0a76416377a2e6f98a2379b210504c730255707624adf78633f427c207fc49e872ff7f652bdc0c28870cfc9126c0a8902c6a269edbed429276cbc98099459fb3b34825f5388802b37f650f611592c49ab49281f44e8c0100472602a8510447f595110cbeec59e8f1d3107d0bffcfb16d231b0164b37ad07002c440ac5510e0df621f1dfbe130199235806f13caf4e142492dff1719";

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//// Отправка сообщения

//$p['method'] = 'sms_send';
//$p['data'] = array(
//    'phone' => '+380676656447',
//    'text' => 'Принеси плиз мне кофе',
//    'convert_to_translit' => 1
//);
//$p['login'] = 'test';
//$p['datetime'] = date('Y-m-d H:i:s');
//$p['sign'] = '';
//$key = "7ac6afc5d17f8e5fe37a568288202574d7dc5ed2f973e7f7f4132a3419e9d766b5b8ea2d73d259680a30ab877edfcc045980d6f8c890456a8bdbc23cbebb344110af2ae9fd389fa47a84f2c5e014e43ac79b4ec89d89f74ce49e945574c355a664d12f8b5b6946ed44dc2b0a76416377a2e6f98a2379b210504c730255707624adf78633f427c207fc49e872ff7f652bdc0c28870cfc9126c0a8902c6a269edbed429276cbc98099459fb3b34825f5388802b37f650f611592c49ab49281f44e8c0100472602a8510447f595110cbeec59e8f1d3107d0bffcfb16d231b0164b37ad07002c440ac5510e0df621f1dfbe130199235806f13caf4e142492dff1719";


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//// Расчет стоимости

//$p['method'] = 'calc_ew_price';
//$p['data'] = array(
//    'point_a_locality_name' => 'Киев',
//    'point_b_locality_name' => 'Киев',
//    'weight' => 1,
//    'max_length' => 50,
//    'size' => 5,
//);
//$p['login'] = 'test';
//$p['datetime'] = date('Y-m-d H:i:s');
//$p['sign'] = '';
//$key = "7ac6afc5d17f8e5fe37a568288202574d7dc5ed2f973e7f7f4132a3419e9d766b5b8ea2d73d259680a30ab877edfcc045980d6f8c890456a8bdbc23cbebb344110af2ae9fd389fa47a84f2c5e014e43ac79b4ec89d89f74ce49e945574c355a664d12f8b5b6946ed44dc2b0a76416377a2e6f98a2379b210504c730255707624adf78633f427c207fc49e872ff7f652bdc0c28870cfc9126c0a8902c6a269edbed429276cbc98099459fb3b34825f5388802b37f650f611592c49ab49281f44e8c0100472602a8510447f595110cbeec59e8f1d3107d0bffcfb16d231b0164b37ad07002c440ac5510e0df621f1dfbe130199235806f13caf4e142492dff1719";

//
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////// Филиальная сеть
//
$p['method'] = 'filials_list';
$p['login'] = 'test';
$p['datetime'] = date('Y-m-d H:i:s');
$p['sign'] = '';
$key = "7ac6afc5d17f8e5fe37a568288202574d7dc5ed2f973e7f7f4132a3419e9d766b5b8ea2d73d259680a30ab877edfcc045980d6f8c890456a8bdbc23cbebb344110af2ae9fd389fa47a84f2c5e014e43ac79b4ec89d89f74ce49e945574c355a664d12f8b5b6946ed44dc2b0a76416377a2e6f98a2379b210504c730255707624adf78633f427c207fc49e872ff7f652bdc0c28870cfc9126c0a8902c6a269edbed429276cbc98099459fb3b34825f5388802b37f650f611592c49ab49281f44e8c0100472602a8510447f595110cbeec59e8f1d3107d0bffcfb16d231b0164b37ad07002c440ac5510e0df621f1dfbe130199235806f13caf4e142492dff1719";
//
//
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//// Филиальная сеть

//$p['method'] = 'branches_locator';
//$p['login'] = 'test';
//$p['filters'] = [
//        'addr'=>'Київ'
//    ];
//$p['datetime'] = date('Y-m-d H:i:s');
//$p['sign'] = '';
//$key = "7ac6afc5d17f8e5fe37a568288202574d7dc5ed2f973e7f7f4132a3419e9d766b5b8ea2d73d259680a30ab877edfcc045980d6f8c890456a8bdbc23cbebb344110af2ae9fd389fa47a84f2c5e014e43ac79b4ec89d89f74ce49e945574c355a664d12f8b5b6946ed44dc2b0a76416377a2e6f98a2379b210504c730255707624adf78633f427c207fc49e872ff7f652bdc0c28870cfc9126c0a8902c6a269edbed429276cbc98099459fb3b34825f5388802b37f650f611592c49ab49281f44e8c0100472602a8510447f595110cbeec59e8f1d3107d0bffcfb16d231b0164b37ad07002c440ac5510e0df621f1dfbe130199235806f13caf4e142492dff1719";

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//// Отримання інформації по ЕН за номером телефону

//$p['method'] = 'get_ew_info';
//$p['login'] = 'test';
//$p['filters'] = array(
//    'phone' => '380999999999',
//    'date_from' => '2019-10-09',
//    'date_to' => '2019-10-12'
//);
//$p['page'] = 1;
//$p['datetime'] = date('Y-m-d H:i:s');
//$p['sign'] = '';
//$key = "7ac6afc5d17f8e5fe37a568288202574d7dc5ed2f973e7f7f4132a3419e9d766b5b8ea2d73d259680a30ab877edfcc045980d6f8c890456a8bdbc23cbebb344110af2ae9fd389fa47a84f2c5e014e43ac79b4ec89d89f74ce49e945574c355a664d12f8b5b6946ed44dc2b0a76416377a2e6f98a2379b210504c730255707624adf78633f427c207fc49e872ff7f652bdc0c28870cfc9126c0a8902c6a269edbed429276cbc98099459fb3b34825f5388802b37f650f611592c49ab49281f44e8c0100472602a8510447f595110cbeec59e8f1d3107d0bffcfb16d231b0164b37ad07002c440ac5510e0df621f1dfbe130199235806f13caf4e142492dff1719";


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//// Отримання повної інформації по ЕН

//$p['method'] = 'get_ew_all_info';
//$p['login'] = 'test';
//$p['filters'] = array(
//    'sender_uuid_1c' => '9973b92a-3b30-11e9-80c5-525400fb7782',
//    'client_number' => '400303267',
//);
//$p['datetime'] = date('Y-m-d H:i:s');
//$p['sign'] = '';
//$key = "7ac6afc5d17f8e5

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if (!isset($p)) $p = '';

$post = json_encode($p) . $key;
$sign = sha1($post, true);
$sign = bin2hex($sign);
$p['sign'] = $sign;

$post = json_encode($p);

//echo $post;

$curl = curl_init();

curl_setopt($curl, CURLOPT_URL, 'http://justin-service-api.local/');

curl_setopt($curl, CURLOPT_RETURNTRANSFER, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // не проверять SSL сертификат
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // не проверять Host SSL сертификата
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
$api = curl_exec($curl);
curl_close($curl);


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//require_once('mustbe.php');
//
//$a = new BDIntJustin();
//print_r($a->getFilials());


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//require_once('mustbe.php');
//
//$a = new BDAutoluxAPI();
////$a->update_shipment();
//print_r($a);


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//require_once('mustbe.php');
//
//$a = new BDLogics('ew_add',array('EW_number'=>100010145847));
//print_r($a);

