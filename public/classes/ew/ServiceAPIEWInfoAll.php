<?php

class ServiceAPIEWInfoAll
{

    public $status;
    public $msg;

    private $result;

    private $buffering_id;

    public $time_update = 10; //  Час оновлення (буферизації) (ХВИЛИНИ)

    function __construct($data)
    {

        $this->status = true;

        $p['sender_uuid'] = (string)$data['filters']['sender_uuid_1c'];
        $p['client_number'] = (string)$data['filters']['client_number'];

        $p = $this->checkData($p);

        $this->checkAddBuffering($p);

        if ($this->status) {
            $this->getInfoFromDB();
        }

        return $p;

    }

    private function checkData($p)
    {

        if (empty(trim($p['sender_uuid']))) {
            $this->status = false;
            $this->msg['code'] = 60501;
        }
        if (empty(trim($p['client_number']))) {
            $this->status = false;
            $this->msg['code'] = 60502;
        }

        return $p;
    }

// Фунція для перевірки/запису буферизації
    private function checkAddBuffering($data)
    {

        $sql = "SELECT * FROM `serviceapi_ew_all_info_buffering` WHERE `sender_uuid` = :sender_uuid AND `client_number` = :client_number LIMIT 1";

        $res = AtomAPIDB::r()->prepare($sql);
        $res->bindParam('sender_uuid', $data['sender_uuid']);
        $res->bindParam('client_number', $data['client_number']);
        $res->execute();
        $one_info = '';
        while ($one = $res->fetch(PDO::FETCH_ASSOC)) {
            $one_info = $one;
        }

        // Змінна для запуску
        $start = 0;

        $check_time = $this->time_update * 60;
        $check_this_time = time();

        // Якщо даних в базі немає - даємо команду на запис
        if (empty($one_info)) {
            $start = 1;
        } else {
            // Якщо час буферизації вийшов - даємо команду на оновлення даних
            if (($check_this_time - $one_info['updatetime']) > $check_time) {
                $start = 1;
            } else {
                if (empty($one_info['json_basic'])) {
                    $start = 1;
                }
                $this->buffering_id = (int)$one_info['id'];
            }
        }

        if ($start == 1 and $this->status == true) {

            // Якщо є запис в базовій буферації - видаляємо його
            if (isset($one_info['id'])) {
                AtomAPIDB::query("DELETE FROM `serviceapi_ew_all_info_buffering` WHERE `id` = " . (int)$one_info['id']);
            }

            // Формуємо запит до БД
            $sql = "INSERT INTO serviceapi_ew_all_info_buffering SET 
                sender_uuid = :sender_uuid,
                client_number = :client_number,
                updatetime = :updatetime
            ";

            // регистрируем запрос
            $res = AtomAPIDB::prepare($sql);

            $res->bindParam('sender_uuid', $data['sender_uuid']);
            $res->bindParam('client_number', $data['client_number']);
            $res->bindValue('updatetime', time());

            // выполняем запрос
            $res->execute();
            $this->buffering_id = AtomAPIDB::r()->lastInsertId();

            // Запускаємо отримання і запис інфо
            $this->getInfo($data);
        }
    }

// Функція отримання інформації від операційного АПІ та її запис в базу
    private function getInfo($data)
    {
        $a = new ServiceAPIJustinApiPMS();

        $ew_info = $a->get_order_info_all($data['sender_uuid'], $data['client_number'], 'test');

        if (isset($ew_info['error']) or isset($ew_info[0]['error'])) {
            $this->status = false;
            $this->msg['code'] = 60510;
        }

        if ($this->status) {
            $json_basic_arr = array(
                'number' => $ew_info['number'],
                'sender_city_uuid_1c' => $ew_info['sender_city_id'],
                'sender_company' => $ew_info['sender_company'],
                'sender_contact' => $ew_info['sender_contact'],
                'sender_phone' => $ew_info['sender_phone'],
                'sender_pick_up_address' => $ew_info['sender_pick_up_address'],
                'pick_up_is_required' => $ew_info['pick_up_is_required'],
                'sender_code_mvv' => $ew_info['sender_branch'],
                'receiver_company' => $ew_info['receiver_company'] ? $ew_info['receiver_company'] : '',
                'receiver_contact' => $ew_info['receiver_contact'],
                'receiver_phone' => $ew_info['receiver_phone'],
                'count_cargo_places' => $ew_info['count_cargo_places'],
                'receiver_code_mvv' => $ew_info['branch'],
                'volume' => $ew_info['volume'],
                'declared_cost' => $ew_info['declared_cost'],
                'delivery_amount' => $ew_info['delivery_amount'],
                'redelivery_amount' => $ew_info['redelivery_amount'],
                'order_amount' => $ew_info['order_amount'],
                'redelivery_payment_is_required' => $ew_info['redelivery_payment_is_required'],
                'redelivery_payment_payer' => $ew_info['redelivery_payment_payer'],
                'delivery_payment_is_required' => $ew_info['delivery_payment_is_required'],
                'add_description' => $ew_info['add_description'],
                'delivery_payment_payer' => $ew_info['delivery_payment_payer'],
                'order_payment_is_required' => $ew_info['order_payment_is_required'],
                'delivery_type' => $ew_info['delivery_type'],
                'cod_transfer_type' => $ew_info['cod_transfer_type'],
                'cod_card_number' => $ew_info['cod_card_number'],
                'cargo_places_array' => $ew_info['cargo_places_array']
            );

            $sql = "UPDATE serviceapi_ew_all_info_buffering SET 
                json_basic = :json_basic
                WHERE `sender_uuid` = :sender_uuid AND `client_number` = :client_number
            ";

            $res = AtomAPIDB::prepare($sql);

            $res->bindParam('json_basic', json_encode($json_basic_arr));
            $res->bindParam('sender_uuid', $data['sender_uuid']);
            $res->bindParam('client_number', $data['client_number']);

            $res->execute();

        }
    }

// Функція отримання інформації з буфера
    private function getInfoFromDB()
    {

        $sql = "SELECT * FROM serviceapi_ew_all_info_buffering WHERE id = " . (int)$this->buffering_id;

        // Записуємо інформацію в масив
        $res = AtomAPIDB::query($sql);
        $r = $res->fetch(PDO::FETCH_ASSOC);
        $this->result = json_decode($r['json_basic'], true);

    }

    public function getResult()
    {
        return $this->result;
    }


}