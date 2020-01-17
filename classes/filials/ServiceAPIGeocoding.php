<?php

class ServiceAPIGeocoding
{
    /**
     *
     * @var type 
     */
    public $status;

    /**
     *
     * @var $msg 
     */
    public $msg;

    /**
     *
     * @var array 
     */
    private $result; //  array

    /**
     *
     * Для помилок
     */
    private $error;

    /**
     *
     * Для формування номеру адреси
     */
    private $forming_number;

    /**
     *
     * Якщо 1 - потрібно віддати дані із буфера, 0 - шукаємо з нуля
     */
    private $finder = 0;

    /**
     *
     * Час оновлення (буферизації) (ХВИЛИНИ)
     */
    public $time_update = 60;

    /**
     *
     * @var type 
     */
    private $google_api_key = "AIzaSyA0xuurSq8On1VSQ4z16_JWkTubt4c9ayM";

    /**
     * Конструктор.
     */
    public function __construct($data){
        $address = urldecode($data['filters']['addr']);
        $this->status = true;
        $this->formingUniqueNumberCode($address);
        $this->checkUniqueNumberCode($address);            
        $this->geocodingAdres($address);
        $this->getInfoLocator();
    }

    /**
     * Поиск адреса в google maps.
     */
    public function geocodingAdres($adres){
        if ($this->finder != 1) {
            $res['original'] = $adres;
            $adres = preg_replace("/[^а-яА-Я\w\d\s,\.\-]/ui",'',$adres);
            $adres = str_replace(' ','+',$adres);
            // проверяем отправку
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://maps.googleapis.com/maps/api/geocode/json?address='.$adres.'&language=ru&region=ua&key='.$this->google_api_key);
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
                // считаем результаты
                foreach($r as $v){ if($v == 'results') $temp++; } 
                // если более одного, то у нас неоднозначность поиска и счиать геокодирование успешным нельзя
                if($temp > 1){ $res['status_geocoding'] = 0; } 
                if($res['status_geocoding'] == -1) {
                    $res['status_geocoding'] = 1;
                    $res['adres'] = $r['results'][0]['formatted_address'];
                    $res['lat'] = $r['results'][0]['geometry']['location']['lat'];
                    $res['lng'] = $r['results'][0]['geometry']['location']['lng'];
                }
                // если статус так и не определили, то значит не нашли
                if($res['status_geocoding'] == -1) $res['status_geocoding'] = 0;
            } else {
                $res['status_google'] = 0;
                $res['status_geocoding'] = 0;
                $res['adres'] = "";
            }

            $this->addBufferingLocator($res);
        }
    }

    /**
     * 
     * Буферизация.
     */
    private function addBufferingLocator($data){
        if ($data['status_geocoding'] == 1) {
            $sql = "
                        INSERT INTO serviceapi_buffering_locator SET
                        `forming_number` = :forming_number,
                        `lat` = :lat,
                        `lng` = :lng,
                        `buffering_time` = :buffering_time
                    ";
            $res = AtomAPIDB::r()->prepare($sql);
            $res->bindValue('forming_number',$this->forming_number);
            $res->bindValue('lat',$data['lat']);
            $res->bindValue('lng',$data['lng']);
            $res->bindValue('buffering_time',time());
            $res->execute();
        } else {
            $this->status = false;
        }
    }

    /**
     * Get Buffer geo data.
     */
    private function getBufferTable(){

        $query = AtomAPIDB::query("SELECT * FROM `serviceapi_buffering_locator` WHERE `forming_number` = '" . $this->forming_number . "'");
        $result = array();
        while($r = $query->fetch(PDO::FETCH_ASSOC)){
            $result = $r;
        }

        return $result;
    }

    /**
     * Функція перевірки номеру в буфері
     */
    private function checkUniqueNumberCode()
    {
        $result = $this->getBufferTable();
        // Якщо знайдено буферизацію
        if(!empty($result)){
            $check_time = $this->time_update * 60;
            $check_this_time = time();
            // Якщо час буферизації вийшов - видаляємо даний запис
            if (($check_this_time - $result['buffering_time']) > $check_time) {
                AtomAPIDB::query("DELETE FROM `serviceapi_buffering_locator` WHERE `forming_number` = '" . $this->forming_number . "'");
            } else { 
                // Якщо ж час буферизації не вийшов - записуємо інформацію про існуючий запис
                $this->finder = 1;
            }
        }
    }

    /**
     * 
     * Формируем уникальный код
     */
    private function formingUniqueNumberCode($str)
    {
        // Переводимо строку в нижній реєстр
        $str = mb_strtolower($str);

        // Масив відповідності букви до цифри
        $array_numbers = array(
            'а' => 1,
            'б' => 2,
            'в' => 3,
            'г' => 4,
            'д' => 5,
            'е' => 6,
            'є' => 7,
            'ж' => 8,
            'з' => 9,
            'и' => 10,
            'і' => 11,
            'ї' => 12,
            'й' => 13,
            'к' => 14,
            'л' => 15,
            'м' => 16,
            'н' => 17,
            'о' => 18,
            'п' => 19,
            'р' => 20,
            'с' => 21,
            'т' => 22,
            'у' => 23,
            'ф' => 24,
            'х' => 25,
            'ц' => 26,
            'ч' => 27,
            'ш' => 28,
            'щ' => 29,
            'ь' => 30,
            'ю' => 31,
            'я' => 32,
            'ы' => 33,
            'ъ' => 34,
            ' ' => 35,
            ',' => 36,
            '.' => 37,
            '\'' => 37
        );
        // Замінюємо всі букви на цифри із роздільником
        foreach($array_numbers as $key => $array_number){
            $str = str_replace($key, $array_number . '|', $str);
        }

        // Створюємо масив із строки по роздільнику
        $explode = explode('|', $str);
        // Формуємо число для даної стрічки
        $forming_number = 0;

        foreach($explode as $exp){
            $forming_number += $exp;
        }

        $this->forming_number = $forming_number;
    }

    /**
     * Получаем инфо для ответа.
     */
    private function getInfoLocator()
    {
        $get_info = $this->getBufferTable();
        // На всякий випадок перевіримо чи отримано інформацію
        if (!empty($get_info)) {
            // Отримуємо найближчі відділення по координатам
            $query = AtomAPIDB::query("SELECT `number`, (6371 * ACOS(COS(RADIANS(" . $get_info['lat'] . ")) * COS( RADIANS(lat)) * COS(RADIANS(lng) - RADIANS(" . $get_info['lng'] . ")) + SIN(RADIANS(" . $get_info['lat'] . ")) * SIN(RADIANS(lat)))) AS distance FROM serviceapi_branches HAVING distance < 2500 ORDER BY distance LIMIT 0,10;");
            while($r = $query->fetch(PDO::FETCH_ASSOC)){
                $branchQuery = AtomAPIDB::query("SELECT * FROM serviceapi_branches WHERE number = " . $r['number']);
                $branch[0] = $branchQuery->fetch(PDO::FETCH_ASSOC);
                $branch_info = $branch;
                unset($branch_info[0]['updatetime']);
                $branch_info[0]['distance'] = round($r['distance'],2);
                $this->result[] = $branch_info[0];
            }
        } else {
            $this->status = false;
            $this->msg['code'] = 10204;
        }
    }

    /**
     * Возвращаем результат.
     */
    public function getResult(){
        return $this->result;
    }
}
