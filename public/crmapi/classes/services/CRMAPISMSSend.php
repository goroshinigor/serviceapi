<?php

class CRMAPISMSSend
{

    public $status;
    public $msg;

    private $result = array();

	public function __construct($data){

        $phoneNumber = (string)$data['phoneNumber'];
        $text = (string)$data['text'];
        $convertToTransilt = (int)$data['convertToTranslit'];

        if($convertToTransilt == 1){

            $alf = array(
                'а' => 'a',   'б' => 'b',   'в' => 'v',
                'г' => 'g',   'д' => 'd',   'е' => 'e',
                'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
                'и' => 'i',   'й' => 'y',   'к' => 'k',
                'л' => 'l',   'м' => 'm',   'н' => 'n',
                'о' => 'o',   'п' => 'p',   'р' => 'r',
                'с' => 's',   'т' => 't',   'у' => 'u',
                'ф' => 'f',   'х' => 'h',   'ц' => 'c',
                'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
                'ь' => '',    'ы' => 'y',   'ъ' => '',
                'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
                'і' => 'i',   'ї' => 'i',   'ґ' => 'g',
                'є' => 'ie',

                'А' => 'A',   'Б' => 'B',   'В' => 'V',
                'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
                'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
                'И' => 'I',   'Й' => 'Y',   'К' => 'K',
                'Л' => 'L',   'М' => 'M',   'Н' => 'N',
                'О' => 'O',   'П' => 'P',   'Р' => 'R',
                'С' => 'S',   'Т' => 'T',   'У' => 'U',
                'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
                'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
                'Ь' => '',    'Ы' => 'Y',   'Ъ' => '',
                'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
                'І' => 'I',   'Ї' => 'Yi',  'Ґ' => 'G',
                'Є' => 'Ye',
            );
            $text = strtr($text, $alf);

        }

        $sms = new CRMAPITurboSMS();
        $res = $sms->sendSMS(array('phone'=>$phoneNumber,'text'=>$text));

        $res = json_decode($res,true); 

        if($res['msg'] == 1) {

            $this->status = true;
            $this->setResult(null);

        } elseif($res['msg'] == 2) {

            $this->status = false;
            $this->msg['code'] = 60300;

        } elseif($res['msg'] == 3) {

            $this->status = false;
            $this->msg['code'] = 60301;

        } elseif($res['msg'] == 4) {

            $this->status = false;
            $this->msg['code'] = 60302;

        }

//    $res = CRMAPIDB::query("SELECT * FROM crmapi_clients WHERE phoneNumber = '".$phoneNumber."' "); $r = $res->fetch(PDO::FETCH_ASSOC);
//    if($r['id'] > 0){


	}


    private function setResult($data = null){
        $this->result = $data;
        return $this->result;
    }


    public function getResult(){
        return $this->result;
    }

}

?>

