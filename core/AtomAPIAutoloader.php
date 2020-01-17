<?php

	function atAutoLoader($class){

		$c[] = ATOM_ROOT.'/'.$class.'.php';
		$c[] = ATOM_ROOT.'/classes/'.$class.'.php';
        $c[] = ATOM_ROOT.'/core/'.$class.'.php';

        // Дістанемо всі підкаталоги каталога classes
		$dirs = glob('classes/*/');

		// Переберемо їх та запишемо в пошуковий масив
		foreach($dirs as $dir){
		    $c[] = ATOM_ROOT.'/'. $dir . $class.'.php';
        }

		$exists = 0;
		foreach($c as $k=>$v){
			if(file_exists($v)){
				$exists = 1;
				require_once($v);
			}
		}

		if($exists == 0){
			echo('Вызов не зарегистрированного класса '.$class);
		}
	}

	spl_autoload_register('atAutoLoader');

?>