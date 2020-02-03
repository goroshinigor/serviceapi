<?php


namespace App\Infrastructure\Services\Legacy;

use PDO;
use Doctrine\ORM\EntityManagerInterface;
use  App\Infrastructure\Services\Legacy\AttikaAPIIntegration;

/**
 * ServiceAPIStatusesList get all statuses from AttikaAPIIntegration
 *  This service injected
 * */

class ServiceApiStatusesList
{
    /**
     * Flag show status
     * @var bool
     * */
    public $status = true;

    /**
     * @var array
     * */
    private $result = [];

    /**
     * This data use in filter and compare
     * */
    private $handbook_data;

    /**
     * Time for update buffer (minute)
     * */
    public $time_update = 0.1;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    /**
     * @var \Doctrine\DBAL\Connection
     * @since 1.1
     */
    private $attika_api;

    /**
     * Inject dependency
     * @param EntityManagerInterface $entityManager
     * @param \App\Infrastructure\Services\Legacy\AttikaAPIIntegration $attika_api
     */
    function __construct(EntityManagerInterface $entityManager, AttikaAPIIntegration $attika_api)
    {
        $this->connection = $entityManager->getConnection();
        $this->attika_api = $attika_api;
    }

    /**
     * Run process
     *
     * @param $data
     * @param string $inside_request
     * @return array
     */
    function run($data = null, $inside_request = 'no')
    {
        if ($inside_request == 'no') {
            $this->checkTimeFromLastUpdate();
        }
        $this->getAll();

        return $this->getResult();
    }

    /**
     * Method for check several params for update data
     * */
    private function checkTimeFromLastUpdate()
    {
        $query = $this->connection->query("SELECT * FROM `serviceapi_handbook_statuses` LIMIT 1");
        $one_info = $query->fetch();

        $check_time = $this->time_update * 60;
        $check_this_time = time();

        $start = 0;

        // Set flag ($start) for recording if DB does not have rec or buffering time is up
        if (empty($one_info) || (is_array($one_info) && (($check_this_time - $one_info['updatetime']) > $check_time)))
            $start = 1;

        if (1 == $start) {
            $this->getInfoApiPMS();
            $this->toDB();
        }
    }

    /**
     * Get all status from JUSTIN API PMS
     * */
    private function getInfoApiPMS()
    {
        $justin = new ServiceAPIJustinApiPMS();
        $pms_all = $justin->get_statuses_list();

        // Simple validate remote data
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

    /**
     * Delete old statuses and Record all status to DB use transaction
     * */
    private function toDB()
    {
        if (is_array($this->handbook_data) and !empty($this->handbook_data)) {
            $this->connection->beginTransaction();

            // Delete old data
            $this->connection->query("DELETE FROM serviceapi_handbook_statuses");
            $ok = false;

            // Recording new data
            foreach ($this->handbook_data as $item) {
                $sql = "INSERT INTO serviceapi_handbook_statuses SET 
                    uuid = :uuid,
                    code = :code,
                    description = :description,
                    is_archive = :is_archive,
                    updatetime = :updatetime";

                $res = $this->connection->prepare($sql);
                $res->bindParam('uuid', $item['uuid']);
                $res->bindParam('code', $item['code']);
                $res->bindParam('description', $item['description']);
                $res->bindParam('is_archive', $item['is_archive']);
                $res->bindValue('updatetime', time());

                $ok = $res->execute();
            }

            if ($ok)
                $ok = $this->updateAttikaInfo();
            if ($ok)
                $this->connection->commit();
            else
                $this->connection->rollBack();
        }
    }

    /**
     * Update status from Attika API
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    private function updateAttikaInfo()
    {
        $ok = false;
        $attika_statuses = $this->attika_api->getStatuses();

        if (isset($attika_statuses['available_platforms']) and !empty($attika_statuses['available_platforms'])) {
            $this->connection->query("DELETE FROM serviceapi_attika_platforms_statuses");
            $res = $this->connection->prepare("INSERT INTO serviceapi_attika_platforms_statuses SET json = :json");
            $json = json_encode($attika_statuses['available_platforms']);
            $res->bindParam('json', $json);
            $res->execute();
        }

        if (isset($attika_statuses['statuses']) and !empty($attika_statuses['statuses'])) {
            $this->connection->query("DELETE FROM serviceapi_handbook_statuses_platforms");

            foreach ($attika_statuses['statuses'] as $attika_status) {
                $sql = "UPDATE serviceapi_handbook_statuses SET 
                    is_archive = :is_archive WHERE uuid = :uuid";
                $res = $this->connection->prepare($sql);
                $res->bindParam('is_archive', $attika_status['is_archive']);
                $res->bindParam('uuid', $attika_status['uuid_1c']);
                $res->execute();

                $sql_two = "INSERT INTO serviceapi_handbook_statuses_platforms SET 
                    uuid = :uuid,
                    code = :code,
                    platforms_json = :platforms_json";
                $res_two = $this->connection->prepare($sql_two);
                $res_two->bindParam('uuid', $attika_status['uuid_1c']);
                $res_two->bindParam('code', $attika_status['code_1c']);
                $json = json_encode($attika_status['platforms']);
                $res_two->bindParam('platforms_json', $json);
                $ok = $res_two->execute();
            }
        }
        return $ok;
    }

    /**
     * Get result of status platform by handbook
     * @return string
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getStatusesPlatforms()
    {
        $res = $this->connection->query("SELECT * FROM serviceapi_attika_platforms_statuses ORDER BY id LIMIT 1");
        $result = $res->fetch(PDO::FETCH_ASSOC);

        if (!isset($result['json']) && empty($result['json']))
            return '';

        return json_decode($result['json'], true);
    }

    /**
     * Get all data from
     * */
    private function getAll()
    {
        $this->result['statuses'] = [];
        $sql = "SELECT * FROM `serviceapi_handbook_statuses` WHERE 1=1";

        $res = $this->connection->prepare($sql);
        $res->execute();

        while ($r = $res->fetch(PDO::FETCH_ASSOC)) {
            // Delete datetime for update
            unset($r['id']);
            unset($r['updatetime']);

            $sqll = "SELECT * FROM `serviceapi_handbook_statuses_platforms` WHERE uuid = :uuid";

            $ress = $this->connection->prepare($sqll);
            $ress->bindParam('uuid', $r['uuid']);
            $ress->execute();
            $tmp_platform = $ress->fetch(PDO::FETCH_ASSOC);

            $this->result['statuses'][$r['uuid']] = $r;

            if (isset($tmp_platform['platforms_json']) and !empty($tmp_platform['platforms_json'])) {
                $this->result['statuses'][$r['uuid']]['platforms'] = json_decode($tmp_platform['platforms_json'], true);
            }
        }

        $this->result['available_platforms'] = $this->getStatusesPlatforms();

        // Sort data
        $titles = array_column($this->result['statuses'], 'code');
        array_multisort($titles, SORT_ASC, $this->result['statuses']);
    }

    /**
     * Get all current result
     * @return array
     * */
    public function getResult()
    {
        return $this->result;
    }


}