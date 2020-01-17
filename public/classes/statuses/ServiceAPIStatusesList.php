<?php

class ServiceAPIStatusesList
{

    public $status = true;
    public $msg;

    private $result;
    private $handbook_data;

//    public $time_update = 60 * 24; //  Час оновлення (буферизації) (ХВИЛИНИ)
    public $time_update = 0.1; //  Час оновлення (буферизації) (ХВИЛИНИ)

    function __construct($data, $inside_request = 'no')
    {
        if ($inside_request == 'no') {
            $this->checkTimeFromLastUpdate();
        }
        $this->getAll();
    }

////// Функція перевірки часу останнього оновлення даних
    // (якщо зазначений час оновлення вийшов - дані будуть оновлені) ///////////////////////////////////////////////////////////////
    private function checkTimeFromLastUpdate()
    {
        $query = AtomAPIDB::r()->query("SELECT * FROM `serviceapi_handbook_statuses` LIMIT 1");
        $one_info = '';
        while ($one = $query->fetch(PDO::FETCH_ASSOC)) {
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
            }
        }

        if ($start == 1) {
            // Отримуємо дані міст по всіх мовах
            $this->getInfoApiPMS();
            $this->toDB();
        }

    }

// Функція отримання інформації із API PMS  ///////////////////////////////////////////////////////////////////////////////////
    private function getInfoApiPMS()
    {
        $justin = new ServiceAPIJustinApiPMS();
        $pms_all = $justin->get_statuses_list();

        // Поставимо додаткові перевірки на випадок непередбачуваних ситуацій
        if (is_array($pms_all)) {
            foreach ($pms_all as $pms_one) {
                if (isset($pms_one['fields'])) {
                    $this->handbook_data[$pms_one['fields']['uuid']]['uuid'] = $pms_one['fields']['uuid'];
                    $this->handbook_data[$pms_one['fields']['uuid']]['code'] = $pms_one['fields']['code'];
                    $this->handbook_data[$pms_one['fields']['uuid']]['description'] = $pms_one['fields']['descr'];
                    $this->handbook_data[$pms_one['fields']['uuid']]['is_archive'] = 0;

                }
            }
        }
    }

// Функція запису довідника в базу ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    private function toDB()
    {

        if (is_array($this->handbook_data) and !empty($this->handbook_data)) {

            AtomAPIDB::beginTransaction();

            // Почистимо довідник
            AtomAPIDB::query("DELETE FROM serviceapi_handbook_statuses");
            $ok = false;
            foreach ($this->handbook_data as $item) {

                // Формуємо запит до БД
                $sql = "INSERT INTO serviceapi_handbook_statuses SET 
                    uuid = :uuid,
                    code = :code,
                    description = :description,
                    is_archive = :is_archive,
                    updatetime = :updatetime
                ";

                // регистрируем запрос
                $res = AtomAPIDB::prepare($sql);

                $res->bindParam('uuid', $item['uuid']);
                $res->bindParam('code', $item['code']);
                $res->bindParam('description', $item['description']);
                $res->bindParam('is_archive', $item['is_archive']);
                $res->bindValue('updatetime', time());

                // выполняем запрос
                $ok = $res->execute();
            }

            if ($ok) {
                $ok = $this->getAttikaInfo();
            }

            // Принимаем решение по транзакции БД
            if ($ok) {
                AtomAPIDB::commit(); // подтверждаем транзакцию в БД
            } else {
                AtomAPIDB::rollBack();
            }
        }
    }

    private function getAttikaInfo(){
        $a = new AttikaAPIIntegration();
        $attika_statuses = $a->getStatuses();
        $ok = false;

        if(isset($attika_statuses['available_platforms']) and !empty($attika_statuses['available_platforms'])){
            AtomAPIDB::query("DELETE FROM serviceapi_attika_platforms_statuses");
            $res = AtomAPIDB::prepare("INSERT INTO serviceapi_attika_platforms_statuses SET json = :json");
            $res->bindParam('json', json_encode($attika_statuses['available_platforms']));
            $res->execute();
        }

        if(isset($attika_statuses['statuses']) and !empty($attika_statuses['statuses'])){
            AtomAPIDB::query("DELETE FROM serviceapi_handbook_statuses_platforms");
            foreach($attika_statuses['statuses'] as $attika_status){

                $sql = "UPDATE serviceapi_handbook_statuses SET 
                    is_archive = :is_archive WHERE uuid = :uuid
                ";
                $res = AtomAPIDB::prepare($sql);
                $res->bindParam('is_archive', $attika_status['is_archive']);
                $res->bindParam('uuid', $attika_status['uuid_1c']);
                // выполняем запрос
                $res->execute();

                $sql_two = "INSERT INTO serviceapi_handbook_statuses_platforms SET 
                    uuid = :uuid,
                    code = :code,
                    platforms_json = :platforms_json
                ";
                $res_two = AtomAPIDB::prepare($sql_two);
                $res_two->bindParam('uuid', $attika_status['uuid_1c']);
                $res_two->bindParam('code', $attika_status['code_1c']);
                $res_two->bindParam('platforms_json', json_encode($attika_status['platforms']));
                // выполняем запрос
                $ok = $res_two->execute();

            }
        }

        return $ok;

    }

    public function getStatusesPlatforms()
    {

        $res = AtomAPIDB::query("
            SELECT * FROM serviceapi_attika_platforms_statuses 
        ");
        $r = $res->fetch(PDO::FETCH_ASSOC);

        return json_decode($r['json'], true);

    }

// Функція отримання всіх даних довідника /////////////////////////////////////////////////////////////////////////////////////////////////////////
    private function getAll()
    {
        $this->result['statuses'] = [];
        $sql = "SELECT * FROM `serviceapi_handbook_statuses` WHERE 1=1";

        $res = AtomAPIDB::r()->prepare($sql);
        $res->execute();

        while ($r = $res->fetch(PDO::FETCH_ASSOC)) {
            // Вирізаємо час для оновлення
            unset($r['id']);
            unset($r['updatetime']);

            $sqll = "SELECT * FROM `serviceapi_handbook_statuses_platforms` WHERE uuid = :uuid";

            $ress = AtomAPIDB::r()->prepare($sqll);
            $ress->bindParam('uuid', $r['uuid']);
            $ress->execute();
            $tmp_platform = $ress->fetch(PDO::FETCH_ASSOC);

            $this->result['statuses'][$r['uuid']] = $r;

            if(isset($tmp_platform['platforms_json']) and !empty($tmp_platform['platforms_json'])){
                $this->result['statuses'][$r['uuid']]['platforms'] = json_decode($tmp_platform['platforms_json'], true);
            }

        }

        $this->result['available_platforms'] = $this->getStatusesPlatforms();

        // Відсортуємо дані
        $titles = array_column($this->result['statuses'], 'code');
        array_multisort($titles, SORT_ASC, $this->result['statuses']);
    }

// Функція повинна бути в кожному методі
    public function getResult()
    {
        return $this->result;
    }


}