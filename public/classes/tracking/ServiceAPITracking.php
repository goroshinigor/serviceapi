<?php

class ServiceAPITracking
{

    public $status = true;
    public $msg;

    private $finder = 0; //  Якщо 1 - потрібно віддати дані із буфера, 0 - шукаємо з нуля
    private $result;

    private $api_key = 'abbaddb8-e42e-11e9-a2d4-c10aa04bb8bc';

    public $time_update = 30; //  Час оновлення (буферизації) (ХВИЛИНИ)

    public $history = 0; //  Якщо 1 - потрібно повернути всю історію замовлення, 0 - повертаємо лише останній статус замовлення
    public $phone = false;

    private $first_status_info = [];
    private $first_status_date = '';

    public function __construct($data, $history = 0)
    {
        $searcher = (string)$data['data']['number'];
        $history = (int)$data['data']['history'];
        $phone = (string)$data['data']['phone'];

        if (empty($searcher)) {
            $this->status = false;
            $this->msg['code'] = 60601;
        } else {
            $this->history = $history;
            $this->phone = $phone;

            // Просто запускаємо оновлення довідника статусів, якщо тому потрібно (швидше за все, потрібно буде прибрати, тому що займає час)
            //$tmp_statuses = new ServiceAPIStatusesList([]);

            $this->checkTimeFromLastUpdate($searcher);
            $this->checkData();
            $this->startTracking($searcher);
            $this->getTrackingInfo($searcher);
        }
    }


    private function checkTimeFromLastUpdate($searcher)
    {

        $sql = "SELECT * FROM `serviceapi_buffering_tracking` WHERE number_ttn = :searcher";

        $query = AtomAPIDB::r()->prepare($sql);
        $query->bindValue('searcher', $searcher);
        $query->execute();

        $one_info = '';
        while ($one = $query->fetch(PDO::FETCH_ASSOC)) {
            $one_info = $one;
        }

        $check_time = $this->time_update * 60;
        $check_this_time = time();

        // Якщо даних в базі немає - створимо запис про буфер та даємо команду на запис
        if (empty($one_info)) {

            $sql = "
				INSERT INTO serviceapi_buffering_tracking SET
				`number_ttn` = :number_ttn,
				`answer_serialize` = :answer_serialize,
				`updatetime` = :updatetime
			";
            $res = AtomAPIDB::r()->prepare($sql);
            $res->bindValue('number_ttn', $searcher);
            $res->bindValue('answer_serialize', '');
            $res->bindValue('updatetime', time());
            $res->execute();

            $this->finder = 0;
        } else {
            // Якщо час буферизації вийшов - видаляємо запис та даємо команду на оновлення даних
            if (($check_this_time - $one_info['updatetime']) > $check_time) {
                AtomAPIDB::r()->query("DELETE FROM `serviceapi_buffering_tracking` WHERE number_ttn = '" . $searcher . "'");

                $sql = "
				INSERT INTO serviceapi_buffering_tracking SET
				`number_ttn` = :number_ttn,
				`answer_serialize` = :answer_serialize,
				`updatetime` = :updatetime
			    ";
                $res = AtomAPIDB::r()->prepare($sql);
                $res->bindValue('number_ttn', $searcher);
                $res->bindValue('answer_serialize', '');
                $res->bindValue('updatetime', time());
                $res->execute();

                $this->finder = 0;
            } else { // Якщо ж час буферизації ще не вийшов - даємо команду на повернення даних у відповідь
                $this->finder = 1;
            }
        }
    }

    private function checkData()
    {
        if ($this->phone) {
            $this->phone = str_replace('+', '', $this->phone);
            $this->phone = str_replace('(', '', $this->phone);
            $this->phone = str_replace(')', '', $this->phone);
            $this->phone = str_replace('-', '', $this->phone);
        }

    }

    private function startTracking($searcher)
    {

        if ($this->finder != 1) {
            // Доступ до API PMS
            $justin = new ServiceAPIJustinApiPMS();

            $filter = array(
                [
                    'name' => 'TTN',
                    'comparison' => 'equal',
                    'leftValue' => $searcher
                ]
            );
            $pms_ttn_statuses = $justin->get_statuses_old($filter);

            if (empty($pms_ttn_statuses)) {
                $filter = array(
                    [
                        'name' => 'orderNumber',
                        'comparison' => 'equal',
                        'leftValue' => $searcher
                    ]
                );

                $pms_ttn_statuses = $justin->get_statuses_old($filter);
            }
            if (empty($pms_ttn_statuses)) {
                $filter = array(
                    [
                        'name' => 'clientNumber',
                        'comparison' => 'equal',
                        'leftValue' => $searcher
                    ]
                );
                $pms_ttn_statuses = $justin->get_statuses_old($filter);
            }

            if (empty($pms_ttn_statuses)) {
                $this->status = false;
                $this->msg['code'] = 60604;
            } else {
                $sql = "
				UPDATE serviceapi_buffering_tracking SET 
				`answer_serialize` = :answer_serialize WHERE 
				`number_ttn` = :number_ttn
			";
                $res = AtomAPIDB::r()->prepare($sql);
                $res->bindValue('number_ttn', $searcher);
                $res->bindValue('answer_serialize', json_encode($pms_ttn_statuses));
                $res->execute();
            }
        }
    }

    private function getTrackingInfo($searcher)
    {

        if ($this->status) {

            $sql = "SELECT * FROM `serviceapi_buffering_tracking` WHERE number_ttn = :searcher";

            $res = AtomAPIDB::r()->prepare($sql);
            $res->bindValue('searcher', $searcher);
            $res->execute();

            $result = array();
            $resul = array();

            while ($r = $res->fetch(PDO::FETCH_ASSOC)) {
                // Вирізаємо час для оновлення
                unset($r['updatetime']);
                $resul = $r;
            }

            $data = json_decode($resul['answer_serialize'], true);

            $this->first_status_info = reset($data);

            $t = explode('T', $this->first_status_info['fields']['statusDate']);
            $this->first_status_date = $t[0];

            $filter_date_from = date('Y-m-d', strtotime($this->first_status_date . ' - 1 days'));
            $filter_date_to = date('Y-m-d', strtotime($this->first_status_date . ' + 1 days'));

            $ew_info = [];

            $receiver_or_sender = '';

            if ($this->phone) {

                if (empty($resul['ew_info'])) {
                    $a = new ServiceAPIJustinApiPMS();

                    $filter = [
                        0 =>
                            [
                                'name' => "date",
                                'comparison' => "between",
                                'leftValue' => $filter_date_from,
                                'rightValue' => $filter_date_to
                            ],
                        1 =>
                            [
                                'name' => "senderPhone",
                                'comparison' => "equal",
                                'leftValue' => $this->phone
                            ]
                    ];

                    $result_orders = $a->get_orders_info($this->api_key, $filter, 1, 50);

                    if (!empty($result_orders['result'])) {
                        foreach ($result_orders['result'] as $one) {
                            if ($one['orderNumber'] == $searcher) {
                                $ew_info = $one;
                                break;
                            } elseif ($one['clientNumber'] == $searcher) {
                                $ew_info = $one;
                                break;
                            } elseif ($one['TTN'] == $searcher) {
                                $ew_info = $one;
                                break;
                            }
                        }
                    } else {
                        $filter = [
                            0 =>
                                [
                                    'name' => "date",
                                    'comparison' => "between",
                                    'leftValue' => $filter_date_from,
                                    'rightValue' => $filter_date_to
                                ],
                            1 =>
                                [
                                    'name' => "receiverPhone",
                                    'comparison' => "equal",
                                    'leftValue' => $this->phone
                                ]
                        ];

                        $result_orders = $a->get_orders_info($this->api_key, $filter, 1, 50);

                        if (!empty($result_orders['result'])) {
                            foreach ($result_orders['result'] as $one) {
                                if ($one['orderNumber'] == $searcher) {
                                    $ew_info = $one;
                                    break;
                                } elseif ($one['clientNumber'] == $searcher) {
                                    $ew_info = $one;
                                    break;
                                } elseif ($one['TTN'] == $searcher) {
                                    $ew_info = $one;
                                    break;
                                }
                            }
                        }
                    }

                    if (!empty($ew_info)) {
                        $sqll = "UPDATE `serviceapi_buffering_tracking` SET ew_info = :ew_info WHERE number_ttn = :searcher";

                        $ress = AtomAPIDB::r()->prepare($sqll);
                        $ress->bindValue('ew_info', json_encode($ew_info));
                        $ress->bindValue('searcher', $searcher);
                        $ress->execute();
                    }


                } else {
                    $ew_info = json_decode($resul['ew_info'], true);
                }

                if (!empty($ew_info)) {
                    if ($ew_info['senderPhone'] == $this->phone) {
                        $receiver_or_sender = "0";
                    } elseif ($ew_info['receiverPhone'] == $this->phone) {
                        $receiver_or_sender = "1";
                    }
                }

            }

            // Якщо потрібно повернути всю історію замовлення
            if ($this->history == 1) {
                foreach ($data as $oneitem) {
                    $result['statuses'][] = $this->formingStatuses($oneitem);
                }
            } else {// В іншому випадку повертаємо лише останній статус замовлення
                $result['statuses'][] = $this->formingStatuses(end($data));
            }

            if ($this->phone) {

                if($receiver_or_sender == ''){
                    $result['phone_info']['is_apply'] = 0;
                }else{
                    $result['phone_info']['is_apply'] = 1;
                }

                $result['phone_info']['relation'] = $receiver_or_sender;

            }

            if (empty($result['statuses'][0]['order_number'])){
                $this->status = false;
                $this->msg['code'] = 60604;
            }

            if ($this->status) {
                if (!empty($result)) {
                    $this->result = $result;
                } else {
                    $this->status = false;
                    $this->msg['code'] = 60604;
                }
            }
        }

    }

    private function formingStatuses($item)
    {

        // Отримаємо інформацію про статуси із довідника
        $tmp_statuses = new ServiceAPIStatusesList([], 'inside');
        $statuses_info = $tmp_statuses->getResult();

// Перелік статусів станом на 13.06.2019
//        b76dd964-4f8f-11e8-80bb-525400fb7782 [000000013] - Запланирован для возврата
//        89e1fe52-94c6-11e8-80c1-525400fb7782 [000000014] - Возвращается отправителю
//        5439ee54-9626-11e8-80c1-525400fb7782 [000000015] - Просрочен срок хранения
//        44408c34-02f9-11e9-80c4-525400fb7782 [000000016] - Переадресовано
//        e7f3ff21-d8fb-11e7-80c6-00155dfbfb00 [000000001] - Планируемый
//        e7f3ff22-d8fb-11e7-80c6-00155dfbfb00 [000000002] - Отменен
//        e7f3ff24-d8fb-11e7-80c6-00155dfbfb00 [000000007] - Находится на отделении
//        e7f3ff25-d8fb-11e7-80c6-00155dfbfb00 [000000003] - Принят на распределительный центр
//        e7f3ff26-d8fb-11e7-80c6-00155dfbfb00 [000000006] - Принят отделением
//        e7f3ff28-d8fb-11e7-80c6-00155dfbfb00 [000000008] - Выдан конечному получателю
//        e7f3ff29-d8fb-11e7-80c6-00155dfbfb00 [000000004] - Отправлен на городской маршрут
//        e7f3ff2a-d8fb-11e7-80c6-00155dfbfb00 [000000009] - Отказ от получения
//        e7f3ff2b-d8fb-11e7-80c6-00155dfbfb00 [000000005] - Отправлен на магистральный маршрут
//        7c7972ae-da6f-11e7-80c6-00155dfbfb00 [000000011] - Спланирован для доставки
//        7c7972af-da6f-11e7-80c6-00155dfbfb00 [000000010] - Спланирован для забора
//        eb1d7e25-e1b5-11e7-80c8-00155dfbfb00 [000000012] - Упакован в контейнер
//        f2c554d3-652e-11e9-80c8-525400fb7782 [000000018] - Отгружен подрядчику
//        f2c554d4-652e-11e9-80c8-525400fb7782 [000000017] - Запланирован для отгрузки подрядчику

        // Отримуємо інформацію про дату і час замовлення
        $date_time_arr = explode('T', $item['fields']['statusDate']);

        $order_description = $item['fields']['order']['descr'];
        $order_status = $item['fields']['statusOrder']['descr'];
        $order_status_uuid = $item['fields']['statusOrder']['uuid'];

        // Якщо статус існує в довіднику - беремо інформацію
        if (isset($statuses_info['statuses'][$order_status_uuid])) {
            $status_info = $statuses_info['statuses'][$order_status_uuid];
        } else { // Якщо ж не існує - вважаємо що статус - ВІДМІНА
            $status_info = $statuses_info['statuses']['e7f3ff22-d8fb-11e7-80c6-00155dfbfb00'];
        }

        // Відмітки для підміни інформації (станом на 01.01.2020)
//        %ew_number%                 Номер ЕН
//        %ew_date%                   Дата создания ЕН
//        %ew_last_status_date%       Дата последнего статуса ЕН
//        %exp_day%                   Срок хранения (в днях)

        $platforms_info = [];

        if (!empty($status_info['platforms'])) {
            foreach ($status_info['platforms'] as $platform) {
                if (!empty($platform)) {
                    foreach ($platform as $k => $v) {
                        $value = str_replace('%ew_number%', $item['fields']['orderNumber'], $v);
                        $value = str_replace('%ew_date%', $this->first_status_date, $value);
                        $value = str_replace('%ew_last_status_date%', $date_time_arr[0], $value);
                        $value = str_replace('%exp_day%', '5', $value);

                        $platforms_info[$platform['platform_alias']][$k] = $value;
                    }
                }
            }
        }

        if ($order_status_uuid == 'e7f3ff22-d8fb-11e7-80c6-00155dfbfb00') {
            $order_status = 'Відміна відправки';
        }

        if ($order_status_uuid == 'e7f3ff21-d8fb-11e7-80c6-00155dfbfb00' || $order_status_uuid == '7c7972af-da6f-11e7-80c6-00155dfbfb00') {
            $order_status = 'Запланована до відправки';
        }

        if ($order_status_uuid == 'e7f3ff24-d8fb-11e7-80c6-00155dfbfb00' || $order_status_uuid == 'e7f3ff25-d8fb-11e7-80c6-00155dfbfb00') {
            $order_status = 'В місті відправника';
        }

        if ($order_status_uuid == 'e7f3ff2b-d8fb-11e7-80c6-00155dfbfb00' || $order_status_uuid == 'e7f3ff29-d8fb-11e7-80c6-00155dfbfb00') {
            $order_status = 'Прямує в місто одержання';
        }

        if ($order_status_uuid == 'eb1d7e25-e1b5-11e7-80c8-00155dfbfb00' || $order_status_uuid == '7c7972ae-da6f-11e7-80c6-00155dfbfb00') {
            $order_status = 'В місті одержувачі';
        }

        if ($order_status_uuid == 'e7f3ff26-d8fb-11e7-80c6-00155dfbfb00') {
            $order_status = 'На відділенні в місті одержання';
        }

        if ($order_status_uuid == 'e7f3ff28-d8fb-11e7-80c6-00155dfbfb00') {
            $order_status = 'Одержано';
        }

        if ($order_status_uuid == '5439ee54-9626-11e8-80c1-525400fb7782') {
            $order_status = 'Прострочений термін зберігання';
        }

        $department_number = $item['fields']['deliveryDepartment'];
        $department_adress = $item['fields']['addressDepartment'];

        // Робимо переклад, який відбудеться, якщо є слово російською
        $department_number = str_replace('Отделение', 'Відділення', $department_number);
        // Масив для повернення
        $arr_return = array(
            'order_number' => $item['fields']['orderNumber'],
            'order_description' => $order_description,
            'date' => $date_time_arr[0],
            'time' => $date_time_arr[1],
            'status' => $order_status,
            'status_uuid_1c' => $item['fields']['statusOrder']['uuid'],
            'department_number' => $department_number,
            'department_address' => $department_adress,
            'platforms' => $platforms_info,
        );

        return $arr_return;


    }

    public function getResult()
    {
        return $this->result;
    }

}