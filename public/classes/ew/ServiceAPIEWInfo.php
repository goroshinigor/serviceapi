<?php

class ServiceAPIEWInfo
{

    public $status;
    public $msg;

    private $result;

    private $api_key = 'abbaddb8-e42e-11e9-a2d4-c10aa04bb8bc';

    private $page_amount = 100; // Кількість записів на сторінці при отриманні інформації з ПМС
    private $basic_id; // Змінна для ідентифікатора запису в базовій таблиці буферизації

    private $page_amount_buffering = 50; // Кількість записів на сторінці при віддачі інформації

    public $time_update = 10; //  Час оновлення (буферизації) (ХВИЛИНИ)

    function __construct($data)
    {

        $this->status = true;

        $p['phone'] = (string)$data['filters']['phone'];
        $p['date_from'] = (string)$data['filters']['date_from'];
        $p['date_to'] = (string)$data['filters']['date_to'];

        // Додаткові фільтри
        $p['ew_number'] = (string)$data['filters']['ew_number'];
        $p['is_incoming'] = (string)$data['filters']['is_incoming'];
        $p['is_archive'] = (string)$data['filters']['is_archive'];
        $p['sender_department_city'] = (string)$data['filters']['sender_department_city'];
        $p['receiver_department_city'] = (string)$data['filters']['receiver_department_city'];
        $p['sender_department_number'] = (string)$data['filters']['sender_department_number'];
        $p['receiver_department_number'] = (string)$data['filters']['receiver_department_number'];
        $p['sender_name'] = (string)$data['filters']['sender_name'];
        $p['receiver_name'] = (string)$data['filters']['receiver_name'];
        $p['receiver_phone'] = (string)$data['filters']['receiver_phone'];
        $p['sender_phone'] = (string)$data['filters']['sender_phone'];
        $p['sender_city'] = (string)$data['filters']['sender_city'];
        $p['receiver_city'] = (string)$data['filters']['receiver_city'];

        $p['status_id'] = (string)$data['filters']['status_id'];

        $p['page'] = (int)$data['page'];

        $p['sorting_date'] = (string)$data['sorting']['date'];

        $p = $this->checkData($p);

        $this->checkAddBasicBuffering($p);

        if ($this->status) {
            $this->getInfoFromDB($p);
        }

        return $p;

    }

    private function checkData($p)
    {

        // Зробимо перевірку чи прийшла інформація про дати фільтрації, якщо ні - ставимо останній місяць
        // Якщо ж дата прийшла - додатково поставимо переформування дати, що прийшла в потрібний нам формат
        $date_mounth = date('Y-m-d', strtotime(' - 30 days'));
        $date_now = date('Y-m-d');
        if (empty($p['date_from'])) {
            $p['date_from'] = $date_mounth;
        } else {
            $p['date_from'] = date("Y-m-d", strtotime($p['date_from']));
        }
        if (empty($p['date_to'])) {
            $p['date_to'] = $date_now;
        } else {
            $p['date_to'] = date("Y-m-d", strtotime($p['date_to']));
        }

        // Якщо сторінка пагінації не прийшла - вважаємо, що сторінка перша
        if (empty($p['page'])) {
            $p['page'] = 1;
        }

        if (empty($p['phone'])) {
            $this->status = false;
            $this->msg['code'] = 60501;
        } else {
            $p['phone'] = str_replace('+', '', $p['phone']);
            $p['phone'] = str_replace('(', '', $p['phone']);
            $p['phone'] = str_replace(')', '', $p['phone']);
            $p['phone'] = str_replace('-', '', $p['phone']);
        }

        if (strlen($p['phone']) < 12) {
            $this->status = false;
            $this->msg['code'] = 60506;
        }

        if (!empty($p['sender_phone'])) {
            $p['sender_phone'] = str_replace('+', '', $p['sender_phone']);
            $p['sender_phone'] = str_replace('(', '', $p['sender_phone']);
            $p['sender_phone'] = str_replace(')', '', $p['sender_phone']);
            $p['sender_phone'] = str_replace('-', '', $p['sender_phone']);
            if (strlen($p['sender_phone']) < 12) {
                $this->status = false;
                $this->msg['code'] = 60506;
            }
        }
        if (!empty($p['receiver_phone'])) {
            $p['receiver_phone'] = str_replace('+', '', $p['receiver_phone']);
            $p['receiver_phone'] = str_replace('(', '', $p['receiver_phone']);
            $p['receiver_phone'] = str_replace(')', '', $p['receiver_phone']);
            $p['receiver_phone'] = str_replace('-', '', $p['receiver_phone']);
            if (strlen($p['receiver_phone']) < 12) {
                $this->status = false;
                $this->msg['code'] = 60506;
            }
        }

        if (isset($p['is_incoming']) and $p['is_incoming'] != '') {
            $p['is_incoming'] = (int)$p['is_incoming'];
        } else {
            $p['is_incoming'] = false;
        }
        if ($p['is_incoming'] > 1) {
            $p['is_incoming'] = false;
        }

        if (isset($p['is_archive']) and $p['is_archive'] != '') {
            $p['is_archive'] = (int)$p['is_archive'];
        } else {
            $p['is_archive'] = false;
        }
        if ($p['is_archive'] > 1) {
            $p['is_archive'] = false;
        }

        // Перевіримо наявність латинських літер в прізвищі чи імені отримувача/відправника
        if (preg_match('/\p{Latin}/u', $p['sender_name'])) {
            $this->status = false;
            $this->msg['code'] = 60507;
        }
        if (preg_match('/\p{Latin}/u', $p['receiver_name'])) {
            $this->status = false;
            $this->msg['code'] = 60507;
        }
        // Перевіримо наявність латинських літер населеному пункті
        if (preg_match('/\p{Latin}/u', $p['sender_city'])) {
            $this->status = false;
            $this->msg['code'] = 60508;
        }
        if (preg_match('/\p{Latin}/u', $p['receiver_city'])) {
            $this->status = false;
            $this->msg['code'] = 60508;
        }

        if (strtolower($p['sorting_date']) == 'asc') {
            $p['sorting_date'] = 'ASC';
        } else {
            $p['sorting_date'] = 'DESC';
        }

        return $p;
    }

// Фунція для перевірки/запису базової буферизації
    private function checkAddBasicBuffering($data)
    {

        $sql = "SELECT * FROM `serviceapi_ew_info_basic_buffering` WHERE `phone` = :phone AND `date_from` = :date_from AND `date_to` = :date_to LIMIT 1";

        $res = AtomAPIDB::r()->prepare($sql);
        $res->bindParam('phone', $data['phone']);
        $res->bindParam('date_from', $data['date_from']);
        $res->bindParam('date_to', $data['date_to']);
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
            } else {// Якщо ж час буферизації не вийшов - дізнаємось чи є взагалі інформація в буфері по команді
                if ($one_info['command'] == 0) { // Якщо інформації в буфері немає - повідомляємо про відсутність
                    $this->status = false;
                    $this->msg['code'] = 60504;
                } else {
                    $this->basic_id = (int)$one_info['id'];
                }
            }
        }

        if ($start == 1 and $this->status == true) {

            // Якщо є запис в базовій буферації - видаляємо його
            if (isset($one_info['id'])) {
                AtomAPIDB::query("DELETE FROM `serviceapi_ew_info_basic_buffering` WHERE `id` = " . (int)$one_info['id']);

                // Також видаляємо всі записи із детальної буферизації
                AtomAPIDB::query("DELETE FROM `serviceapi_ew_info_detail_buffering` WHERE `basic_id` = " . (int)$one_info['id']);
            }

            // Формуємо запит до БД
            $sql = "INSERT INTO serviceapi_ew_info_basic_buffering SET 
                phone = :phone,
                date_from = :date_from,
                date_to = :date_to,
                command = :command,
                updatetime = :updatetime
            ";

            // регистрируем запрос
            $res = AtomAPIDB::prepare($sql);

            $res->bindParam('phone', $data['phone']);
            $res->bindParam('date_from', $data['date_from']);
            $res->bindParam('date_to', $data['date_to']);
            $res->bindValue('command', 0);
            $res->bindValue('updatetime', time());

            // выполняем запрос
            $res->execute();
            $this->basic_id = AtomAPIDB::r()->lastInsertId();

            // Запускаємо отримання і запис інфо
            $this->getInfo($data);
        }
    }

// Функція отримання інформації від операційного АПІ та її запис в базу
    private function getInfo($data)
    {
        $a = new ServiceAPIJustinApiPMS();

        $filter_receiver = array(
            0 =>
                array(
                    'name' => "date",
                    'comparison' => "between",
                    'leftValue' => $data['date_from'],
                    'rightValue' => $data['date_to']
                ),
            1 =>
                array(
                    'name' => "receiverPhone",
                    'comparison' => "equal",
                    'leftValue' => $data['phone']
                ),
        );
        $filter_sender = array(
            0 =>
                array(
                    'name' => "date",
                    'comparison' => "between",
                    'leftValue' => $data['date_from'],
                    'rightValue' => $data['date_to']
                ),
            1 =>
                array(
                    'name' => "senderPhone",
                    'comparison' => "equal",
                    'leftValue' => $data['phone']
                ),
        );

        // Масив для receiver
        $data_receiver = array();
        // Масив для sender
        $data_sender = array();

        $ews_receiver = $a->get_orders_info($this->api_key, $filter_receiver, 1, $this->page_amount);
        $ews_sender = $a->get_orders_info($this->api_key, $filter_sender, 1, $this->page_amount);

        // Якщо дані не пусті - записуємо їх в основні масиви
        if (!empty($ews_receiver['result'])) {
            $data_receiver = $ews_receiver['result'];
        }
        if (!empty($ews_sender['result'])) {
            $data_sender = $ews_sender['result'];
        }

        // Якщо інформації більше ніж на 1 сторінці
        if ($ews_receiver['pagination']['pages'] > 1) {
            for ($i = 2; $i <= $ews_receiver['pagination']['pages']; $i++) {
                $ews_receiver_tmp = $a->get_orders_info($this->api_key, $filter_receiver, $i, $this->page_amount);

                if (!empty($ews_receiver_tmp['result'])) {
                    $data_receiver = array_merge($data_receiver, $ews_receiver_tmp['result']);
                }
            }
        }
        if ($ews_sender['pagination']['pages'] > 1) {
            for ($i = 2; $i <= $ews_sender['pagination']['pages']; $i++) {
                $ews_sender_tmp = $a->get_orders_info($this->api_key, $filter_sender, $i, $this->page_amount);

                if (!empty($ews_sender_tmp['result'])) {
                    $data_sender = array_merge($data_sender, $ews_sender_tmp['result']);
                }
            }
        }

        // Якщо масиви із інформацією пусті - виводимо повідомлення про відсутність інформації
        if (empty($data_receiver) and empty($data_sender)) {
            $this->status = false;
            $this->msg['code'] = 60504;
        } else { // Якщо ж не пусті - готуємо все до запису та записуємо інформацію в табличку детальної буферизації

            $st = new ServiceAPIStatusesList(array(), 'yes');
            $statuses = $st->getResult();

            AtomAPIDB::beginTransaction();

            $ok = false;
            $receiver_ok = false;
            $sender_ok = false;

            // Змінна для розуміння, що масиви з інформацією не пусті - тобто можна оновити запис в базовій буфериазції
            $allow_update = false;

            // Якщо масив із вхідними ЕН не пустий - перебіраємо його і передаємо в функцію запису
            if (!empty($data_receiver)) {
                $allow_update = true;
                foreach ($data_receiver as $item) {
                    $receiver_ok = $this->addDetailBuffering($item, 1, $statuses);
                }
            } else {
                $receiver_ok = true;
            }

            // Якщо масив із вихідними ЕН не пустий - перебіраємо його і передаємо в функцію запису
            if (!empty($data_sender)) {
                $allow_update = true;
                foreach ($data_sender as $item) {
                    $sender_ok = $this->addDetailBuffering($item, 0, $statuses);
                }
            } else {
                $sender_ok = true;
            }

            if ($receiver_ok and $sender_ok) {
                $ok = true;
            } else {
                $this->status = false;
                $this->msg['code'] = 60505;
            }

            if ($allow_update) {
                // Оновимо інформацію про те, що дані в буферизації є
                AtomAPIDB::query("UPDATE serviceapi_ew_info_basic_buffering SET command = 1 WHERE id = " . (int)$this->basic_id);
            }

            // Принимаем решение по транзакции БД
            if ($ok) {
                AtomAPIDB::commit(); // подтверждаем транзакцию в БД
            } else {
                AtomAPIDB::rollBack();
            }

        }
    }

// Функція запису в таблицю детальної буферизації
    private function addDetailBuffering($data, $sender_or_receiver, $statuses)
    {

        if (strpos($data['date'], 'T')) {
            $date_tmp = explode('T', $data['date']);
            $data['date'] = $date_tmp[0];
        }
        if (strpos($data['dateStatus'], 'T')) {
            $date_tmp = explode('T', $data['dateStatus']);
            $data['dateStatus'] = $date_tmp[0];
        }

        // Відразу дістанемо інформацію по філіалах
        $sql = "SELECT * FROM `serviceapi_pms_branches` WHERE `filial_uuid` = :filial_uuid LIMIT 1";

        $res_department_sender = AtomAPIDB::r()->prepare($sql);
        $res_department_sender->bindParam('filial_uuid', $data['senderDeliveryDepartment']);
        $res_department_sender->execute();
        $department_sender_data = $res_department_sender->fetch(PDO::FETCH_ASSOC);

        $warehouse_arr = array(
            '10a0ca94-6769-11e9-80c8-525400fb7782' => array(
                'number' => 'РЦ Київ',
                'address' => 'Київська, Київ, Чистяківська вул., 30',
                'geo' => array(
                    'locality_ua' => 'Київ'
                )
            ),
            '01be2c2a-bfd2-11e8-80c4-525400fb7782' => array(
                'number' => 'РЦ Дніпро',
                'address' => 'Дніпропетровська, Дніпро, Сухомлинського Василя вул., 78б',
                'geo' => array(
                    'locality_ua' => 'Дніпро'
                )
            )
        );

        $department_sender_info = array(
            'number' => '',
            'address' => '',
            'geo' => array(
                'locality_ua' => ''
            )
        );

        // На всякий випадок перевіримо чи є інформація про дане відділення
        if (!empty($department_sender_data) and isset($department_sender_data['json_basic'])) {
            $department_sender_info = json_decode($department_sender_data['json_basic'], true);
        } elseif (isset($warehouse_arr[$data['senderDeliveryDepartment']])) {
            $department_sender_info = $warehouse_arr[$data['senderDeliveryDepartment']];
        }

        $res_department_receiver = AtomAPIDB::r()->prepare($sql);
        $res_department_receiver->bindParam('filial_uuid', $data['receiverDeliveryDepartment']);
        $res_department_receiver->execute();
        $department_receiver_data = $res_department_receiver->fetch(PDO::FETCH_ASSOC);

        $department_receiver_info = array(
            'number' => '',
            'address' => '',
            'geo' => array(
                'locality_ua' => ''
            )
        );

        // На всякий випадок перевіримо чи є інформація про дане відділення
        if (!empty($department_receiver_data) and isset($department_receiver_data['json_basic'])) {
            $department_receiver_info = json_decode($department_receiver_data['json_basic'], true);
        } elseif (isset($warehouse_arr[$data['receiverDeliveryDepartment']])) {
            $department_sender_info = $warehouse_arr[$data['receiverDeliveryDepartment']];
        }

        // Розберемо ПІПи
        $sender_name_arr = explode(' ', $data['senderContactName']);
        $receiver_name_arr = explode(' ', $data['receiverContactName']);

        if (empty($sender_name_arr) or count($sender_name_arr) < 3) {
            $sender_name_arr = array(
                0 => $data['senderContactName'],
                1 => '',
                2 => ''
            );
        }
        if (empty($receiver_name_arr) or count($receiver_name_arr) < 3) {
            $receiver_name_arr = array(
                0 => $data['receiverContactName'],
                1 => '',
                2 => ''
            );
        }

        if ($data['delivery_type'] == 0) {
            $delivery_type = 'B2C';
        } elseif ($data['delivery_type'] == 1) {
            $delivery_type = 'B2C';
        } elseif ($data['delivery_type'] == 2) {
            $delivery_type = 'C2C';
        } elseif ($data['delivery_type'] == 3) {
            $delivery_type = 'C2B';
        } else {
            $delivery_type = 'DeliveryPayment';
        }

        $cod_commission_external = $data['CODPayment'] - $data['redelivery_amount'];
        if (($data['DeliveryPayment'] - $data['DeliveryPaymentReceived']) > 0) {
            $delivery_payment_status = 0;
        } else {
            $delivery_payment_status = 1;
        }

        if ($data['CODPaymentReceived'] == 0) {
            $cod_payment_status = 0;
            $cod_commission_payment_status = 0;
        } elseif (($data['CODPaymentReceived'] - $data['CODPayment']) == 0) {
            $cod_payment_status = 1;
            $cod_commission_payment_status = 1;
        } elseif (($data['CODPaymentReceived'] - $data['redelivery_amount']) == 0) {
            $cod_payment_status = 1;
            $cod_commission_payment_status = 0;
        } elseif (($data['CODPaymentReceived'] - $cod_commission_external) == 0) {
            $cod_payment_status = 0;
            $cod_commission_payment_status = 1;
        } else {
            $cod_payment_status = 0;
            $cod_commission_payment_status = 0;
        }

        $cost_pay_sender = 0;
        $cost_pay_receiver = $data['redelivery_amount'];

        if ($data['delivery_payment_payer'] == 0) {
            $cost_pay_sender += $data['DeliveryPayment'];
        } else {
            $cost_pay_receiver += $data['DeliveryPayment'];
        }
        if ($data['redelivery_payment_payer'] == 0) {
            $cost_pay_sender += $cod_commission_external;
        } else {
            $cost_pay_receiver += $cod_commission_external;
        }

        if ($data['statusOrder'] == '00000000-0000-0000-0000-000000000000') {
            $data['statusOrder'] = 'e7f3ff22-d8fb-11e7-80c6-00155dfbfb00';
        }

        $attr_array = array(
            'basic_id' => (int)$this->basic_id,
            'is_archive' => (int)$data['final'],
            'is_incoming' => (int)$sender_or_receiver,
            'sender_uuid_1c' => $data['sender'],
            'sender_phone' => $data['senderPhone'],
            'sender_full_name' => trim($sender_name_arr[0] . ' ' . $sender_name_arr[1] . ' ' . $sender_name_arr[2]),
            'sender_first_name' => $sender_name_arr[1],
            'sender_second_name' => $sender_name_arr[2],
            'sender_last_name' => $sender_name_arr[0],
            'sender_company' => $data['senderName'],
            'receiver_uuid_1c' => $data['receiver'],
            'receiver_phone' => $data['receiverPhone'],
            'receiver_full_name' => trim($receiver_name_arr[0] . ' ' . $receiver_name_arr[1] . ' ' . $receiver_name_arr[2]),
            'receiver_first_name' => $receiver_name_arr[1],
            'receiver_second_name' => $receiver_name_arr[2],
            'receiver_last_name' => $receiver_name_arr[0],
            'receiver_company' => $data['receiverName'],
            'sender_department_uuid_1c' => $data['senderDeliveryDepartment'],
            'sender_department_number' => $department_sender_info['number'],
            'sender_department_address' => $department_sender_info['address'],
            'sender_department_city' => $department_sender_info['geo']['locality_ua'],
            'receiver_department_uuid_1c' => $data['receiverDeliveryDepartment'],
            'receiver_department_number' => $department_receiver_info['number'],
            'receiver_department_address' => $department_receiver_info['address'],
            'receiver_department_city' => $department_receiver_info['geo']['locality_ua'],
            'ew_number' => $data['orderNumber'],
            'client_number' => $data['clientNumber'],
            'ttn' => $data['TTN'],
            'order_date' => date("Y-m-d H:i:s", strtotime($data['date'])),
            'description' => $data['add_description'],
            'delivery_type' => $delivery_type,
            'status_id' => $statuses['statuses'][$data['statusOrder']]['code'],
            'status_description' => $statuses['statuses'][$data['statusOrder']]['description'],
            'status_date' => date("Y-m-d H:i:s", strtotime($data['dateStatus'])),
            'weight' => $data['weight'],
            'max_size' => $data['max_size'],                                        // Максимальна сторона (см)
            'type_size' => $data['type'],
            'count_cargo_places' => $data['count_cargo_places'],                    // Кількість вантажних місць
            'delivery_payment' => $data['DeliveryPayment'],
            'delivery_payment_received' => $data['DeliveryPaymentReceived'],
            'delivery_payment_payer' => $data['delivery_payment_payer'],            // Платник доставки
            'delivery_payment_status' => (int)$delivery_payment_status,             // Статус оплати доставки
            'declared_cost' => $data['declared_cost'],                              // Оголошена вартість
            'cod_payment' => $data['CODPayment'],
            'cod_payment_received' => $data['CODPaymentReceived'],
            'cod_summ' => $data['redelivery_amount'],                               // Сума COD
            'cod_commission_external' => $cod_commission_external,                  // Сума комісії COD
            'cod_commission_external_payer' => $data['redelivery_payment_payer'],   // Платник комісії COD
            'cod_is_available' => $data['CODPayment'] > 0 ? 1 : 0,                  // Наявність COD
            'cod_delivery_type' => (int)$data['cod_transfer_type'],                 // Спосіб повернення COD
            'cod_card_number' => $data['cod_card_number'],                          // Номер карти для повернення COD
            'cod_payment_status' => (int)$cod_payment_status,                       // Статус оплати COD
            'cod_commission_payment_status' => (int)$cod_commission_payment_status, // Статус оплати комісії COD
            'cost_pay_sender' => $cost_pay_sender,                                  // Сумма послуг до сплати відправником
            'cost_pay_receiver' => $cost_pay_receiver                               // Сумма послуг до сплати отримувачем
        );


        // Формуємо запит до БД
        $sql_insert = "INSERT INTO serviceapi_ew_info_detail_buffering SET ";

        foreach ($attr_array as $attr_key => $attr_val) {
            $sql_insert .= $attr_key . " = :" . $attr_key . ", ";
        }

        $sql_insert = substr($sql_insert, 0, -2);

        $res = AtomAPIDB::prepare($sql_insert);

        foreach ($attr_array as $attr_key => $attr_val) {
            $res->bindValue($attr_key, $attr_val);
        }

        $ok = $res->execute();

        return $ok;

    }

// Функція отримання інформації з буфера
    private function getInfoFromDB($data)
    {

        $this->result['ews'] = array();

        $filter_sql = '';

        if ($data['is_incoming'] !== false) {
            $filter_sql .= ' AND `is_incoming` = :is_incoming';
        }
        if ($data['is_archive'] !== false) {
            $filter_sql .= ' AND `is_archive` = :is_archive';
        }
        if (!empty($data['ew_number'])) {
            $filter_sql .= ' AND `ew_number` = :ew_number';
        }
        if (!empty($data['status_id'])) {
            $filter_sql .= ' AND `status_id` = :status_id';
        }
        if (!empty($data['sender_phone'])) {
            $filter_sql .= ' AND `sender_phone` = :sender_phone AND `is_incoming` = 1';
        }
        if (!empty($data['receiver_phone'])) {
            $filter_sql .= ' AND `receiver_phone` = :receiver_phone AND `is_incoming` = 0';
        }
        if (!empty($data['sender_department_number'])) {
            $filter_sql .= ' AND `sender_department_number` = :sender_department_number AND `is_incoming` = 1';
        }
        if (!empty($data['receiver_department_number'])) {
            $filter_sql .= ' AND `receiver_department_number` = :receiver_department_number AND `is_incoming` = 0';
        }
        if (!empty($data['sender_name'])) {
            $filter_sql .= ' AND (`sender_last_name` LIKE concat("%",:sender_name,"%") OR `sender_second_name` LIKE concat("%",:sender_name,"%") OR `sender_first_name` LIKE concat("%",:sender_name,"%")) AND `is_incoming` = 1';
        }
        if (!empty($data['receiver_name'])) {
            $filter_sql .= ' AND (`receiver_last_name` LIKE concat("%",:receiver_name,"%") OR `receiver_second_name` LIKE concat("%",:receiver_name,"%") OR `receiver_first_name` LIKE concat("%",:receiver_name,"%")) AND `is_incoming` = 0';
        }
        if (!empty($data['sender_city'])) {
            $filter_sql .= ' AND (`sender_department_city` LIKE concat("%",:sender_city,"%") OR `sender_department_address` LIKE concat("%",:sender_city,"%")) AND `is_incoming` = 1';
        }
        if (!empty($data['receiver_city'])) {
            $filter_sql .= ' AND (`receiver_department_city` LIKE concat("%",:receiver_city,"%") OR `receiver_department_address` LIKE concat("%",:receiver_city,"%")) AND `is_incoming` = 0';
        }


        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM serviceapi_ew_info_detail_buffering WHERE 
                `basic_id` = :basic_id 
                " . $filter_sql . " 
                ORDER BY order_date " . (string)$data['sorting_date'] . " 
                LIMIT " . ($data['page'] - 1) * $this->page_amount_buffering . ", " . $this->page_amount_buffering;


        $res = AtomAPIDB::r()->prepare($sql);
        $res->bindValue('basic_id', (int)$this->basic_id);

        if ($data['is_incoming'] !== false) {
            $res->bindValue('is_incoming', (int)$data['is_incoming']);
        }
        if ($data['is_archive'] !== false) {
            $res->bindValue('is_archive', (int)$data['is_archive']);
        }
        if (!empty($data['ew_number'])) {
            $res->bindValue('ew_number', (string)$data['ew_number']);
        }
        if (!empty($data['status_id'])) {
            $res->bindValue('status_id', (string)$data['status_id']);
        }
        if (!empty($data['sender_phone'])) {
            $res->bindValue('sender_phone', (string)$data['sender_phone']);
        }
        if (!empty($data['receiver_phone'])) {
            $res->bindValue('receiver_phone', (string)$data['receiver_phone']);
        }
        if (!empty($data['sender_department_number'])) {
            $res->bindValue('sender_department_number', (string)$data['sender_department_number']);
        }
        if (!empty($data['receiver_department_number'])) {
            $res->bindValue('receiver_department_number', (string)$data['receiver_department_number']);
        }
        if (!empty($data['sender_name'])) {
            $res->bindValue('sender_name', (string)$data['sender_name']);
        }
        if (!empty($data['receiver_name'])) {
            $res->bindValue('receiver_name', (string)$data['receiver_name']);
        }
        if (!empty($data['sender_city'])) {
            $res->bindValue('sender_city', (string)$data['sender_city']);
        }
        if (!empty($data['receiver_city'])) {
            $res->bindValue('receiver_city', (string)$data['receiver_city']);
        }

        $res->execute();

        // Записуємо інформацію в масив
        while ($r = $res->fetch(PDO::FETCH_ASSOC)) {
            unset($r['basic_id']);
            unset($r['id']);
            $this->result['ews'][] = $r;
        }

        $objects_count = AtomAPIDB::r()->query("SELECT FOUND_ROWS();")->fetchColumn();

        // Записуємо інформацію про пагінацію
        $this->result['pagination'] = array(
            'page' => $data['page'],
            'pages' => ceil($objects_count / $this->page_amount_buffering),
            'page_amount' => $this->page_amount_buffering
        );

        if (empty($this->result['ews'])) {
            $this->status = false;
            $this->msg['code'] = 60510;
            $this->result = null;
        }

    }


    public function getResult()
    {
        return $this->result;
    }


}