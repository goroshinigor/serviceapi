<?php

require_once("mustbe.php");
require_once("classes/calculator/ServiceAPICalculator.php");

$d['point_a_locality_name'] = 'Киев';
$d['point_b_locality_name'] = 'Киев2';

$d['weight'] = 0.3;
$d['max_length'] = 50;
//    $d['size'] = 50;

$d['cod'] = 115;
$d['estcost'] = 250;

$a = new ServiceAPICalculator($d);

print_r($a);
