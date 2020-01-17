<?php

//	ini_set('display_errors',1);
//	error_reporting(E_ERROR+E_COMPILE_ERROR+E_CORE_ERROR+E_PARSE+E_USER_ERROR);


$v2AlowedMethods = [
    'branches_locator',
    'client_verify_phone',
    'c2c_create_sending',
    'add_sending_to_observed',
    'remove_sending_from_observed',
    'get_observers_list'
];

$json = json_decode(file_get_contents('php://input'));

if ((in_array($json->method, $v2AlowedMethods))) {
    $_SERVER['DOCUMENT_URI'] = '/v2.php';
    $_SERVER['REQUEST_URI'] = '/v2';
    $_SERVER['SCRIPT_NAME'] = '/v2.php';
    $_SERVER['SCRIPT_FILENAME'] = dirname(__FILE__) . '/v2.php';
    $_SERVER['PHP_SELF'] = '/v2.php';
    require 'v2.php';
} else {
    session_start();

    require_once("mustbe.php");

    $a = new AtomAPI();
    $a->set(file_get_contents("php://input"));
    echo $a->gogo();
}


