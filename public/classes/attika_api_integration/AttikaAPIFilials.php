<?php

class AttikaAPIFilials
{

    public function getFilials()
    {

        $res = AtomAPIDB::query("
            SELECT * FROM serviceapi_attika_filials 
            WHERE filial_number > 1 AND update_datetime > '" . date('Y-m-d H:i:s', strtotime('-5 minute')) . "' 
            LIMIT 1 
        ");
        $r = $res->fetch();

        if ($r['filial_number'] == 0) {

            $a = new AttikaAPIIntegration();
            $res = $a->getFilials();

            foreach ($res as $k => $v) {
                $numbers[] = $k;
            }

            $res = $a->getFilialInfo($numbers);

            if ($a->status) {
                AtomAPIDB::query("DELETE FROM serviceapi_attika_filials");
                foreach ($res as $k => $v) {
                    if ($k > 0) {
                        $v['basic'] = array(
                            'schedule' => $v['schedule'],
                        );
                        $res = AtomAPIDB::prepare("INSERT INTO serviceapi_attika_filials SET 
                            filial_number = '" . $k . "',
                            json_basic = :basic,
                            json_services = :services,
                            json_public = :public,
                            json_photos = :photos,
                            update_datetime = '" . date('Y-m-d H:i:s') . "'
                        ");
                        $res->bindParam('basic', json_encode($v['basic']));
                        $res->bindParam('services', json_encode($v['services']));
                        $res->bindParam('public', json_encode($v['public']));
                        $res->bindParam('photos', json_encode($v['photos']));
                        $res->execute();
                    }
                    if ($k == 'available_services') {
                        AtomAPIDB::query("DELETE FROM serviceapi_attika_filial_services");
                        $res = AtomAPIDB::prepare("INSERT INTO serviceapi_attika_filial_services SET json = :json");
                        $res->bindParam('json', json_encode($v));
                        $res->execute();
                    }
                }
            }

        }

        $res = AtomAPIDB::query("SELECT * FROM serviceapi_attika_filials ORDER BY filial_number");
        while ($r = $res->fetch(PDO::FETCH_ASSOC)) {
            if ($r['filial_number'] > 0) {
                $filials[$r['filial_number']]['number'] = $r['filial_number'];
                $filials[$r['filial_number']]['basic'] = json_decode($r['json_basic'], true);
                $filials[$r['filial_number']]['public'] = json_decode($r['json_public'], true);
                $filials[$r['filial_number']]['photos'] = json_decode($r['json_photos'], true);
                $filials[$r['filial_number']]['services'] = json_decode($r['json_services'], true);
            }
        }

        return (array)$filials;

    }

    public function getFilialServices()
    {

        $res = AtomAPIDB::query("
            SELECT * FROM serviceapi_attika_filial_services 
        ");
        $r = $res->fetch(PDO::FETCH_ASSOC);

        return json_decode($r['json'], true);

    }

}