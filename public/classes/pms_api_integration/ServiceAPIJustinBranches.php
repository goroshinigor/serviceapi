<?php

class ServiceAPIJustinBranches{

    function __construct(){

    }

    public function getBranches(){

        $res = AtomAPIDB::query("
            SELECT * FROM serviceapi_pms_branches 
            WHERE filial_number > 1 AND update_datetime > '".date('Y-m-d H:i:s',strtotime('-1 minute'))."' 
            LIMIT 1 
        ");
        $r = $res->fetch();

        if($r['filial_number'] == 0) {

            $a = new ServiceAPIJustinApiPMS();
            $branches = $a->get_departments();

            if(count($branches) > 0){

                AtomAPIDB::query("DELETE FROM serviceapi_pms_branches");
                foreach($branches as $v){

                    $filial_uuid = $v['fields']['Depart']['uuid'];

                    $t['name'] = $v['fields']['descr'];
                    $t['1c_uuid'] = $v['fields']['Depart']['uuid'];
                    $t['number'] = $v['fields']['departNumber'];
                    $t['type_number'] = $v['fields']['TypeDepart']['value'];
                    $t['type_name'] = $v['fields']['TypeDepart']['enum'];
                    $t['code_mvv'] = $v['fields']['branch'];
                    $t['weight_max'] = $v['fields']['weight_limit'];

                    $t['format'] = $v['fields']['branchType']['descr'];

                    $t['address'] = $v['fields']['address'];
                    $t['lat'] = $v['fields']['lat'];
                    $t['lng'] = $v['fields']['lng'];

                    $t['geo']['region_ua'] = $v['fields']['region']['descr'];
                    $t['geo']['region_scoatou'] = $v['fields']['regionSCOATOU'];
                    $t['geo']['area_ua'] = $v['fields']['area']['descr'];
                    $t['geo']['area_scoatou'] = $v['fields']['areaSCOATOU'];
                    $t['geo']['locality_ua'] = $v['fields']['city']['descr'];
                    $t['geo']['locality_scoatou'] = $v['fields']['citySCOATOU'];
                    $t['geo']['locality_ua'] = $v['fields']['city']['descr'];
                    $t['geo']['locality_scoatou'] = $v['fields']['citySCOATOU'];
                    $t['geo']['district_ua'] = $v['fields']['locality']['descr'];
                    $t['geo']['district_scoatou'] = $v['fields']['localitySCOATOU'];
                    $t['geo']['street_ua'] = $v['fields']['street']['descr'];
                    $t['geo']['house_ua'] = $v['fields']['houseNumber'];

                    $res = AtomAPIDB::prepare("INSERT INTO serviceapi_pms_branches SET 
                            filial_number = '".$t['number']."',
                            filial_uuid = '".$filial_uuid."',
                            json_basic = :basic,
                            update_datetime = '".date('Y-m-d H:i:s')."'
                        ");
                    $res->bindParam('basic',json_encode($t));
                    $res->execute();

                }


            }

        }

        $res = AtomAPIDB::query("SELECT * FROM serviceapi_pms_branches ORDER BY filial_number");
        while($r = $res->fetch(PDO::FETCH_ASSOC)){
            if($r['filial_number'] > 0){
                $filials[$r['filial_number']]['number'] = $r['filial_number'];
                $filials[$r['filial_number']] = json_decode($r['json_basic'],true);
            }
        }

        return $filials;

    }


}