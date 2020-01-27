<?php

//	ini_set('display_errors',1);
//	error_reporting(E_ERROR+E_COMPILE_ERROR+E_CORE_ERROR+E_PARSE+E_USER_ERROR);

    header('Content-Type: text/html; charset=utf8');
    session_start();

    require_once("mustbe.php");

    $a = new AtomAPI();
    $a->set(file_get_contents("php://input"));
    echo $a->gogo();

