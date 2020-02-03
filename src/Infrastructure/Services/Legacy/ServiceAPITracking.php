<?php


namespace App\Infrastructure\Services\Legacy;


use App\Domain\DTO\ServiceApiResponseResultDTO;
use PDO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validation;
use App\Infrastructure\Services\Api\ApiService;
use Symfony\Component\Validator\Constraints as Assert;
use App\Infrastructure\Services\Legacy\ServiceAPIJustinApiPMS;
use App\Infrastructure\Services\Validation\ServiceValidatePhone;
use App\Infrastructure\Services\Legacy\ServiceApiStatusesList;

class ServiceAPITracking
{
    /**
     * API KEY for access to justin api
     * */
    private $api_key = 'abbaddb8-e42e-11e9-a2d4-c10aa04bb8bc';

    /**
     * @var bool
     * */
    public $status = true;

    /**
     * Error message
     * */
    public $msg;

    /**
     * @var bool  If true -- we must delete data from buffer, false -- search by zero
     * */
    private $finder = false;

    /**
     *
     * */
    private $result;

    /**
     * Time for update buffer (minute)
     * */
    public $time_update = 30;

    /**
     * @var bool  If true -- we must get full history, false --get only latest row
     * */
    public $history = false;

    /**
     * $client phone
     * @var string
     * */
    public $phone = null;

    /**
     * $client search code of order
     * @var string
     * */
    public $searcher = null;

    /**
     * @var array
     */
    private $errors;

    /**
     *
     * */
    private $first_status_info = [];

    /**
     *
     * */
    private $first_status_date = '';

    /**
     * Query parameter
     * @var \stdClass|null
     * */
    protected $data = null;


    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    /**
     * @var ServiceValidatePhone
     */
    private $phonevalidator;

    /**
     * @var ServiceApiStatusesList
     */
    private $serviceAPIStatusesList;

    /**
     * Inject dependency
     *
     * @param EntityManagerInterface $entityManager
     * @param ServiceValidatePhone $validatePhoneService
     * @param ServiceApiStatusesList $serviceAPIStatusesList
     */
    public function __construct(EntityManagerInterface $entityManager,
                                ServiceValidatePhone $validatePhoneService,
                                ServiceApiStatusesList $serviceAPIStatusesList)
    {
        $this->connection = $entityManager->getConnection();
        $this->phonevalidator = $validatePhoneService;
        $this->serviceAPIStatusesList = $serviceAPIStatusesList;
    }

    /**
     * @param ApiService $apiService
     * @param bool $history
     * @return
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Exception
     */
    public function run(ApiService $apiService)
    {
        $this->data = $apiService->getRequestParams()->data ?? null;

        if (isset($this->data)) {

            $this->validator($this->data);

            $this->searcher = (string)$this->data->number ?? null;
            $this->history = (1 == ($this->data->history ?? null));
            $this->phone = (string)$this->data->phone ?? null;

            $this->checkTimeFromLastUpdate($this->searcher);

            $this->startTracking($this->searcher);
            $this->getTrackingInfo($this->searcher);

            if ($this->status == false) {
                $ex = current($this->errors);
                $msg = is_array($ex) ? implode("++", $ex['msg']) : $ex;
                $code = (is_array($ex) && !empty($ex['code'])) ? $ex['code'] : '0000';

                throw new \Exception($msg, $code);
            }

            return new ServiceApiResponseResultDTO($this->result ?? []);

        }
    }


    /**
     * Validate query data
     *
     * @param \stdClass $data
     */
    private function validator(\stdClass $data)
    {
        $validator = Validation::createValidator();

        // Validate number
        $simpleConstraints = [
            new Assert\NotBlank([
                'message' => 'tracking.number.not_blank',
            ]),
            new Assert\Type([
                'type' => 'numeric',
                'message' => 'tracking.number.type [number] [{{ type }}]']),

            new Assert\Length([
                'min' => 1,
                'max' => 9,
                'minMessage' => 'tracking.number.min  [number]  [{{ limit }}]',
                'maxMessage' => 'tracking.number.max  [number]  [{{ limit }}]',
            ])
        ];

        $errors = $validator->validate(
            ($data->number ?? null),
            $simpleConstraints
        );
        foreach ($errors as $error) {
            if (empty($error)) continue;
            $error = $this->errorTranslate((string)$error);
            $this->errors[] = $error;
        }

        // Validate history Choice
        if (isset($data->history)) {
            $simpleConstraints = [
                new Assert\Choice([
                    'choices' => [0, 1],
                    'message' => 'tracking.history.choice [{{ value }}] [{{ choices }}]',
                ]),
            ];

            $errors = $validator->validate(
                $data->history,
                $simpleConstraints
            );

            foreach ($errors as $error) {
                if (empty($error)) continue;
                $error = $this->errorTranslate((string)$error);
                $this->errors[] = $error;
            }
        }

        // Validate phone number if exists
        if (!empty($data->phone)) {
            if (!$this->phonevalidator::validate($data->phone)) {
                $this->errors[] = $this->errorTranslate('tracking.phone.error');
            }
        }
    }


    /**
     * Check time from last update. Update buffer tracker If last update more 30 min.
     * Also create new buffer tracker if not found
     *
     * @param string $searcher
     * @throws \Doctrine\DBAL\DBALException
     */
    private function checkTimeFromLastUpdate(string $searcher)
    {
        if (true == $this->status) {
            $sql = "SELECT * FROM `serviceapi_buffering_tracking` WHERE number_ttn = :searcher";

            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue('searcher', $searcher);
            $stmt->execute();
            $one_info = $stmt->fetch();

            $check_time = $this->time_update * 60;
            $check_this_time = time();

            // Create new buffer tracker if current not exist
            if (empty($one_info)) {

                $sql = "
				INSERT INTO serviceapi_buffering_tracking SET
				`number_ttn` = :number_ttn,
				`answer_serialize` = :answer_serialize,
				`updatetime` = :updatetime";

                $stmt = $this->connection->prepare($sql);
                $stmt->bindValue('number_ttn', $searcher);
                $stmt->bindValue('answer_serialize', '');
                $stmt->bindValue('updatetime', time());
                $stmt->execute();

                $this->finder = false;
            } else {
                // Delete current row and set flag for update data
                // if time of buffering more $check_time (30 min)
                if (($check_this_time - $one_info['updatetime']) > $check_time) {

                    $this->connection->query("DELETE FROM `serviceapi_buffering_tracking` WHERE number_ttn = '" . $searcher . "'");

                    $sql = "INSERT INTO serviceapi_buffering_tracking SET
				`number_ttn` = :number_ttn,
				`answer_serialize` = :answer_serialize,
				`updatetime` = :updatetime";

                    $stmt = $this->connection->prepare($sql);
                    $stmt->bindValue('number_ttn', $searcher);
                    $stmt->bindValue('answer_serialize', '');
                    $stmt->bindValue('updatetime', time());
                    $stmt->execute();

                    $this->finder = false;
                } else {
                    // set flag for command "get current data"
                    // if time of buffering less $check_time (30 min)
                    $this->finder = true;
                }
            }
        }
    }


    /**
     * @param string $searcher
     * @throws \Doctrine\DBAL\DBALException
     */
    private function startTracking(string $searcher)
    {
        if (false == $this->finder && true == $this->status) {

            $justin = new ServiceAPIJustinApiPMS();

            /**
             * $sender this is required item for anyone tracking request
             * if use method get_statuses -- "getOrderStatusesHistoryF"
             * */
            $sender = [
                "name" => "senderId",
                "comparison" => "not",
                "leftValue" => "abbaddb8-e42e-11e9-a2d4-c10aa04bb8bc"
            ];

            $filter = array(
                $sender, [
                    'name' => 'TTN',
                    'comparison' => 'equal',
                    'leftValue' => $searcher
                ]
            );
            $pms_ttn_statuses = $justin->get_statuses($filter);

            if (empty($pms_ttn_statuses)) {
                $filter = array(
                    $sender, [
                        'name' => 'orderNumber',
                        'comparison' => 'equal',
                        'leftValue' => $searcher
                    ]
                );

                $pms_ttn_statuses = $justin->get_statuses($filter);
            }
            if (empty($pms_ttn_statuses)) {
                $filter = array(
                    $sender, [
                        'name' => 'clientNumber',
                        'comparison' => 'equal',
                        'leftValue' => $searcher
                    ]
                );
                $pms_ttn_statuses = $justin->get_statuses($filter);
            }

            if (empty($pms_ttn_statuses)) {
                $this->errors[] = $this->errorTranslate("order.not_found [$searcher]");
            } else {
                $sql = "UPDATE serviceapi_buffering_tracking SET 
				`answer_serialize` = :answer_serialize WHERE 
				`number_ttn` = :number_ttn";
                $res = $this->connection->prepare($sql);
                $res->bindValue('number_ttn', $searcher);
                $res->bindValue('answer_serialize', json_encode($pms_ttn_statuses));
                $res->execute();
            }
        }
    }


    /**
     * Get info by Tracking
     *
     * @param $searcher
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getTrackingInfo($searcher)
    {
        if (true == $this->status) {

            $result = array();

            $sql = "SELECT * FROM `serviceapi_buffering_tracking` WHERE number_ttn = :searcher";

            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue('searcher', $searcher);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            unset($res['updatetime']);

            $data = json_decode($res['answer_serialize'], true);

            if (empty($data)) {
                $this->errors[] = $this->errorTranslate('system.error');
                return;
            }

            $this->first_status_info = reset($data);

            $date_time = explode('T', $this->first_status_info['fields']['statusDate']);
            $this->first_status_date = $date_time[0];

            $filter_date_from = date('Y-m-d', strtotime($this->first_status_date . ' - 1 days'));
            $filter_date_to = date('Y-m-d', strtotime($this->first_status_date . ' + 1 days'));

            $ew_info = [];

            $receiver_or_sender = '';

            if ($this->phone) {

                if (empty($res['ew_info'])) {
                    $justin_api = new ServiceAPIJustinApiPMS();

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

                    $result_orders = $justin_api->get_orders_info($this->api_key, $filter, 1, 50);

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

                        $result_orders = $justin_api->get_orders_info($this->api_key, $filter, 1, 50);

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
                        $sql = "UPDATE `serviceapi_buffering_tracking` SET ew_info = :ew_info WHERE number_ttn = :searcher";

                        $ress = $this->connection->prepare($sql);
                        $ress->bindValue('ew_info', json_encode($ew_info));
                        $ress->bindValue('searcher', $searcher);
                        $ress->execute();
                    }

                } else {
                    $ew_info = json_decode($res['ew_info'], true);
                }

                if (!empty($ew_info)) {
                    if ($ew_info['senderPhone'] == $this->phone) {
                        $receiver_or_sender = "0";
                    } elseif ($ew_info['receiverPhone'] == $this->phone) {
                        $receiver_or_sender = "1";
                    }
                }
            }

            /**
             * Get all history if history flag is true
             * and only latest if history flag false
             * */
            if ($this->history === true) {
                foreach ($data as $onetime) {
                    $result['statuses'][] = $this->formingStatuses($onetime);
                }
            } else {
                $result['statuses'][] = $this->formingStatuses(end($data));
            }

            if ($this->phone) {
                if ($receiver_or_sender == '') {
                    $result['phone_info']['is_apply'] = 0;
                } else {
                    $result['phone_info']['is_apply'] = 1;
                }
                $result['phone_info']['relation'] = $receiver_or_sender;
            }

            if (!isset($result['statuses'][0]) && empty($result['statuses'][0]['order_number'])) {
                $this->errors[] = $this->errorTranslate("order.not_found [$searcher]");
            }

            if ($this->status && !empty($result)) {
                $this->result = $result;
            }
        }
    }


    /**
     * Formating result
     *
     * @param $item
     * @return array
     */
    private function formingStatuses($item)
    {
        /**
         * inside -- get all statuses from DB
         * no -- get all statuses from API
         * */
        $this->serviceAPIStatusesList->run([], 'inside');
        $statuses_info = $this->serviceAPIStatusesList->getResult();

        if (empty($statuses_info) || empty($statuses_info['statuses'])) {
            return null;
        }

        /*
         *  Перелік статусів станом на 13.06.2019
                b76dd964-4f8f-11e8-80bb-525400fb7782 [000000013] - Запланирован для возврата
                89e1fe52-94c6-11e8-80c1-525400fb7782 [000000014] - Возвращается отправителю
                5439ee54-9626-11e8-80c1-525400fb7782 [000000015] - Просрочен срок хранения
                44408c34-02f9-11e9-80c4-525400fb7782 [000000016] - Переадресовано
                e7f3ff21-d8fb-11e7-80c6-00155dfbfb00 [000000001] - Планируемый
                e7f3ff22-d8fb-11e7-80c6-00155dfbfb00 [000000002] - Отменен
                e7f3ff24-d8fb-11e7-80c6-00155dfbfb00 [000000007] - Находится на отделении
                e7f3ff25-d8fb-11e7-80c6-00155dfbfb00 [000000003] - Принят на распределительный центр
                e7f3ff26-d8fb-11e7-80c6-00155dfbfb00 [000000006] - Принят отделением
                e7f3ff28-d8fb-11e7-80c6-00155dfbfb00 [000000008] - Выдан конечному получателю
                e7f3ff29-d8fb-11e7-80c6-00155dfbfb00 [000000004] - Отправлен на городской маршрут
                e7f3ff2a-d8fb-11e7-80c6-00155dfbfb00 [000000009] - Отказ от получения
                e7f3ff2b-d8fb-11e7-80c6-00155dfbfb00 [000000005] - Отправлен на магистральный маршрут
                7c7972ae-da6f-11e7-80c6-00155dfbfb00 [000000011] - Спланирован для доставки
                7c7972af-da6f-11e7-80c6-00155dfbfb00 [000000010] - Спланирован для забора
                eb1d7e25-e1b5-11e7-80c8-00155dfbfb00 [000000012] - Упакован в контейнер
                f2c554d3-652e-11e9-80c8-525400fb7782 [000000018] - Отгружен подрядчику
                f2c554d4-652e-11e9-80c8-525400fb7782 [000000017] - Запланирован для отгрузки подрядчику
         * */

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

        /**
         * TODO deliveryDepartment is un exists
         * TODO addressDepartment is un exists
         * */
        $department_number = $item['fields']['deliveryDepartment'] ?? '';
        $department_adress = $item['fields']['addressDepartment'] ?? '';

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


    /**
     * Translate vocabulary
     * Get localize translate by key
     *
     * @param string $error
     * @return array|string
     */
    private function errorTranslate(string $error = '')
    {
        $this->status = false;


        $err = explode("\n", trim($error));
        $error = trim(end($err));
        $default = $error;

        preg_match_all("/\[([^\]]*)\]/", $error, $matches);
        $error = preg_replace("/\[([^\]]*)\]/", "", $error);
        $error = preg_replace("/\([^)]+\)/", "", $error);

        $vocabulary = $this->getErrorMsgPatterns() ?? [];

        $path = explode('.', trim($error));

        /**
         * This recursive function return array of error messages
         * @param $path
         * @param array $vocabulary
         * @return array|mixed
         */
        $field = '';
        $recursive = function ($path, array $vocabulary) use (&$recursive, &$field) {
            if (is_array($vocabulary)) {
                if (is_array($path)) {
                    reset($path);
                    $key = array_shift($path);
                    if (count($path) == 1) {
                        $field = $key;
                    }
                    if (!empty($vocabulary[$key])) {
                        if (empty($path)) return $vocabulary[$key];
                        return $recursive($path, $vocabulary[$key]);
                    }
                } else {
                    return $vocabulary;
                }
            } else {
                return [];
            }
        };

        /**
         * Return default string If vocabulary un include translate
         * */
        $result = $recursive($path, $vocabulary) ?? $default;

        /**
         * Set Value to local string if value exist
         * */
        if (is_array($result) && is_array($result['msg']) && !empty($matches[1])) {
            $result['field'] = $field;
            foreach ($matches[1] as $replace) {
                $from = '/' . preg_quote(':?', '/') . '/';
                $result['msg'] = preg_replace($from, $replace, $result['msg'], 1);
            }
        }

        return $result;
    }


    /**
     * Error message pattern
     * */
    public function getErrorMsgPatterns()
    {
        return [
            'tracking' => [
                'number' => [
                    "not_blank" => [
                        'msg' => [
                            'ru' => "Номер накладной является обязательным",
                            'ua' => "Номер накладної є обов'язковим",
                            'en' => "Invoice number is required"
                        ],
                        'code' => 60430
                    ],

                    "min" => [
                        'msg' => [
                            'ru' => "Минимальноеколичество символов в поле ':?' - :?",
                            'ua' => "Мінімальна кількість символів в полі ':?' - :?",
                            'en' => "Minimum count charts in field ':?' - :?"
                        ],
                        'code' => 60430
                    ],

                    "max" => [
                        'msg' => [
                            'ru' => "Максимальное количество символов в поле ':?' - :?",
                            'ua' => "Максимальна кількість символів в полі ':?' - :?",
                            'en' => "Maximum count charts in field ':?' - :?"
                        ],
                        'code' => 60430
                    ],
                    'type' => [
                        'msg' => [
                            'ru' => "Значение в поле ':?' не является действительным :?.",
                            'ua' => "Значення в полі ':?' не є дійсний :?",
                            'en' => "The value in field ':?' is not a valid :?."
                        ],
                        'code' => 60430
                    ]
                ],
                'history' => [
                    'choice' => [
                        'msg' => [
                            'ru' => "Выбранное вами значение в поле (:?) Не является допустимым [:?]",
                            'ua' => "Вибране значення в полі (:?) Не є правильним вибором [:?]",
                            'en' => "The value in field (:?) is not a valid choice [:?]"
                        ],
                        'code' => 60430
                    ]
                ],
                'phone' => [
                    'error' => [
                        'msg' => [
                            'ru' => "Указанный телефон не соответствует формату +380999999999",
                            'ua' => "Зазначений телефон не відповідає формату +380999999999",
                            'en' => "The specified phone does not match the format +380999999999"
                        ],
                        'code' => 60201
                    ]
                ],
            ],
            'order' => [
                'not_found' => [
                    'msg' => [
                        'ru' => "Не удалось найти отправление :?",
                        'ua' => "Не вдалося знайти відправлення :?",
                        'en' => "Could not find a shipment :?"
                    ],
                    'code' => 60440
                ]
            ],
            'system' => [
                'error' => [
                    'msg' => [
                        'ru' => "Произошел системный сбой платформы. Обратитесь в службу поддержки",
                        'ua' => "Стався системний збій платформи. Зверніться в службу підтримки",
                        'en' => "System error. Send request or call to support"
                    ],
                    'code' => 60020
                ]
            ]
        ];
    }
}