<?php

class AtomWee
{

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Функция ковертирования даты к указанному типу
	public static function dateConverter($date_string,$to_format){

		if($date_string == ''){ $date_string == "0000-00-00 00:00:00"; }
		$result = (string)$date_string;

		if(preg_match("/^(\d{2}).(\d{2}).(\d{4}) (\d{2}):(\d{2}):(\d{2})$/",$date_string,$preg_array)){
			$cur_format = "ua_datetime";
			$cur_d = $preg_array[1];
			$cur_m = $preg_array[2];
			$cur_Y = $preg_array[3];
			$cur_H = $preg_array[4];
			$cur_i = $preg_array[5];
			$cur_s = $preg_array[6];
		} elseif(preg_match("/^(\d{2}).(\d{2}).(\d{4}) (\d{2}):(\d{2})$/",$date_string,$preg_array)){
			$cur_format = "ua_date";
			$cur_d = $preg_array[1];
			$cur_m = $preg_array[2];
			$cur_Y = $preg_array[3];
			$cur_H = $preg_array[4];
			$cur_i = $preg_array[5];
			$cur_s = '00';
		} elseif(preg_match("/^(\d{2}).(\d{2}).(\d{4})$/",$date_string,$preg_array)){
			$cur_format = "ua_date";
			$cur_d = $preg_array[1];
			$cur_m = $preg_array[2];
			$cur_Y = $preg_array[3];
			$cur_H = '00';
			$cur_i = '00';
			$cur_s = '00';
		} elseif(preg_match("/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/",$date_string,$preg_array)){
			$cur_format = "db_datetime";
			$cur_Y = $preg_array[1];
			$cur_m = $preg_array[2];
			$cur_d = $preg_array[3];
			$cur_H = $preg_array[4];
			$cur_i = $preg_array[5];
			$cur_s = $preg_array[6];
		} elseif(preg_match("/^(\d{4})-(\d{2})-(\d{2})$/",$date_string,$preg_array)){
			$cur_format = "db_date";
			$cur_Y = $preg_array[1];
			$cur_m = $preg_array[2];
			$cur_d = $preg_array[3];
			$cur_H = '00';
			$cur_i = '00';
			$cur_s = '00';
		} else {
			$cur_format = 'undefined';
		}

		if($cur_format != 'undefined') {

			switch ($to_format) {
				case "ua_datetime":
					if(($cur_Y + $cur_m + $cur_d + $cur_H + $cur_i + $cur_s) > 0) {
						$result = date('d.m.Y H:i:s', mktime($cur_H, $cur_i, $cur_s, $cur_m, $cur_d, $cur_Y));
					} else {
						$result = "00.00.0000 00:00:00";
					}
					break;
				case "ua_date":
					if(($cur_Y + $cur_m + $cur_d) > 0) {
						$result = date('d.m.Y', mktime($cur_H, $cur_i, $cur_s, $cur_m, $cur_d, $cur_Y));
					} else {
						$result = "00.00.0000";
					}
					break;
				case "iso_datetime":
					if(($cur_Y + $cur_m + $cur_d + $cur_H + $cur_i + $cur_s) > 0) {
						$result = date('Y-m-d H:i:s',mktime($cur_H,$cur_i,$cur_s,$cur_m,$cur_d,$cur_Y));
					} else {
						$result = "0000-00-00 00:00:00";
					}
					break;
				case "iso_date":
					if(($cur_Y + $cur_m + $cur_d) > 0) {
						$result = date('Y-m-d', mktime($cur_H, $cur_i, $cur_s, $cur_m, $cur_d, $cur_Y));
					} else {
						$result = "0000-00-00";
					}
					break;
			}

		}

		return $result;
	}


	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Функция преобразования даты в секунды от 01/01/1970
	public static function getSecFromDatetime($x,$type){
		if($type == 'db_datetime'){
			// 0123-56-89 1112:1415:00
			if($x != "0000-00-00 00:00:00" and $x != ''){
				$s_d = substr($x,8,2);
				$s_m = substr($x,5,2);
				$s_y = substr($x,0,4);
				$s_h = substr($x,11,2);
				$s_i = substr($x,14,2);
				$s_s = '00';
				$temp = mktime($s_h,$s_i,$s_s,$s_m,$s_d,$s_y);
			} else {
				$temp = 0;
			}
		}
		if($type == 'db_date'){
			// 0123-56-89
			if($x != "0000-00-00" and $x != ''){
				$s_d = substr($x,8,2);
				$s_m = substr($x,5,2);
				$s_y = substr($x,0,4);
				$s_h = '00';
				$s_i = '00';
				$s_s = '00';
				$temp = mktime($s_h,$s_i,$s_s,$s_m,$s_d,$s_y);
			} else {
				$temp = 0;
			}
		}
		return  $temp;
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Функция преобразования между двумя таймштампами в дни, часы, минуты, секунды
	public static function getTimestampsDiff($timestamp1,$timestamp2){

		$diff = $sec = abs($timestamp1 - $timestamp2);

		$d = floor($sec/24/3600);
		$sec = $sec - ($d*24*3600);
		$h = floor($sec/3600);
		$sec = $sec - ($h*3600);
		$m = floor($sec/60);
		$s = $sec - ($m*60);

		if(preg_match("/.*1$/",$d)) $dn = 'день';
		if(preg_match("/.*[234]$/",$d)) $dn = 'дня';
		if(preg_match("/.*[567890]$/",$d)) $dn = 'дней';
		if($d > 10 and $d < 20) $dn = 'дней';

		if(preg_match("/.*1$/",$h)) $hn = 'час';
		if(preg_match("/.*[234]$/",$h)) $hn = 'часа';
		if(preg_match("/.*[567890]$/",$h)) $hn = 'часов';
		if($h > 10 and $h < 20) $hn = 'часов';

		if(preg_match("/.*1$/",$m)) $mn = 'минута';
		if(preg_match("/.*[234]$/",$m)) $mn = 'минуты';
		if(preg_match("/.*[567890]$/",$m)) $mn = 'минут';
		if($m > 10 and $m < 20) $mn = 'минут';

		if(preg_match("/.*1$/",$s)) $sn = 'секунда';
		if(preg_match("/.*[234]$/",$s)) $sn = 'секунды';
		if(preg_match("/.*[567890]$/",$s)) $sn = 'секунд';
		if($s > 10 and $s < 20) $sn = 'секунд';

		if($d > 0) $human_label_1 = $d." ".$dn;
		if($d == 0 and $h >= 20) $human_label_1 = "менее дня";
		if($d == 0 and $h < 20) $human_label_1 = $h." ".$hn;
		if($d == 0 and $h == 0 and $m >= 50) $human_label_1 = "менее часа";
		if($d == 0 and $h == 0 and $m <= 50) $human_label_1 = $m." ".$mn;
		if($d == 0 and $h == 0 and $m == 0) $human_label_1 = "менее минуты";

		$arr = array(
			'days' => $d,
			'days_label' => $dn,
			'hours' => $h,
			'hours_label' => $hn,
			'min' => $m,
			'min_label' => $mn,
			'sec' => $s,
			'sec_label' => $sn,
			'human_label_1' => $human_label_1,
			'diff_in_seconds' => $diff
		);

		return  $arr;
	}

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public static function generateGUID(){
		if (function_exists('com_create_guid')){
			$uuid = com_create_guid();
		} else {
			mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
			$charid = md5(uniqid(rand(), true));
			$hyphen = chr(45);// "-"
			$uuid = ""
				.substr($charid, 0, 8).$hyphen
				.substr($charid, 8, 4).$hyphen
				.substr($charid,12, 4).$hyphen
				.substr($charid,16, 4).$hyphen
				.substr($charid,20,12);
		}
		return $uuid;
	}

}

?>