<?php

/**
 * Class AtomGeocoding
 *
 * @version 1.0
 */
class AtomGeocoding
{

	public $status;
	public $msg;

    private $google_api_key = "AIzaSyBB9U-MN_N5t-euHezbPoKPbZ7QKCJqPKA"; // dev.postua@gmail.com - POST API Geocoding


    /**
     * AtomGeocoding constructor.
     */
	public function __construct(){
		$this->status = true;
	}


    /**
     * Main method geocoding addresses
     *
     * @param $adres
     * @param string $search_level default "street_number"
     * @return array
     *
     */
	public function geocodingAdres($adres,$search_level="street_number"){

		$res['original'] = $adres;

		$adres = preg_replace("/[^а-яА-Я\w\d\s,\.\-]/ui",'',$adres); // очищаем адрес
		$adres = str_replace(' ','+',$adres); // вместо пробелов ставим +

        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.$adres.'&language=ru&region=ua&key='.$this->google_api_key;

		// запрос
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // не проверять SSL сертификат
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // не проверять Host SSL сертификата
		$r = curl_exec($curl);
		curl_close($curl);

		$res['status_geocoding'] = -1; // -1 обозначаем как статус неопределенности, не да и не нет.
		$res['json'] = $r;
		$r = json_decode($r,true);

		// анализ результата
		// выполнено ли кодирование
		if($r['status'] == 'OK'){
			$res['status_google'] = 1;

			// проверяем кол-во полученных результатов
			$temp = 0;
			foreach($r as $v){ if($v == 'results') $temp++; } // считаем результаты
			if($temp > 1){ $res['status_geocoding'] = 0; } // если более одного, то у нас неоднозначность поиска и счиать геокодирование успешным нельзя

			if($res['status_geocoding'] == -1){

                // фиксируем адрес форматированный, если в ответ пишел один адрес
                $res['address'] = $r['results'][0]['formatted_address'];

                // проверяем уровень глубины результата поиска адреса до номера дома
                if($search_level == 'street_number') {
                    foreach ($r['results'][0]['address_components'] as $v) {
                        foreach ($v['types'] as $types) {
                            if ($types == 'street_number') {
                                // если среди результатов поиска есть параметр отвечающий за номер дома, то геокодирование прошло успешно
                                $res['status_geocoding'] = 1;
                            }
                        }
                    }
                } else {
                    // если до номера дома искать не надо, то статус гекодирования точно будет положительным
                    $res['status_geocoding'] = 1;
                }

			}

			// если статус так и не определили, то значит не нашли
			if($res['status_geocoding'] == -1) $res['status_geocoding'] = 0;

		} else {
			$res['status_google'] = 0;
			$res['status_geocoding'] = 0;
			$res['address'] = "";
		}

		return (array)$res;

	}


}

?>