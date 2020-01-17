<?php

require_once("mustbe.php");
require_once("classes/pms_api_integration/ServiceAPIJustinApiPMS.php");

//$a = new ServiceAPIJustinApiPMS();
//print_r($a->get_departments());

//$a = new ServiceAPIJustinBranches();
//print_r($a->getBranches());

//$a = new AttikaAPIIntegration();
//print_r($a->getFilials());

//$a = new AttikaAPIFilials();
//print_r($a->getFilials());

$a = new ServiceAPIFilialsList();
print_r($a->getFilials());