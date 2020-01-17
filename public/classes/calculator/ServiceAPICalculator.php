<?php

class ServiceAPICalculator
{

    public $status;
    public $msg;

    private $result;

    public function __construct($data)
    {

        $this->status = true;

        $p['point_a_locality_name'] = (string)$data['point_a_locality_name'];
        $p['point_a_locality_uuid'] = (string)$data['point_a_locality_uuid'];
        $p['point_a_locality_scoatou'] = (string)$data['point_a_locality_scoatou'];

        $p['point_b_locality_name'] = (string)$data['point_b_locality_name'];
        $p['point_b_locality_uuid'] = (string)$data['point_b_locality_uuid'];
        $p['point_b_locality_scoatou'] = (string)$data['point_b_locality_scoatou'];

        $p['max_length'] = (int)$data['max_length']; // см
        $p['weight'] = (float)$data['weight']; // кг
        $p['size'] = (string)$data['size']; // типоразмер

        $p['cod'] = (float)$data['cod']; // грн
        $p['estcost'] = (float)$data['estcost']; // грн

        $p = $this->checkData($p);

        if ($this->status) {
            $this->setResult($this->calculate($p));
        }

        return $p;
    }

    private function checkData($p)
    {

        if ($this->status and $p['size'] == '' and $p['max_length'] <= 0) {
            $this->status = false;
            $this->msg['code'] = 60401;
        } // Не указана максимальная длина ЭН
        if ($this->status and $p['size'] == '' and $p['weight'] <= 0) {
            $this->status = false;
            $this->msg['code'] = 60402;
        } // Не указан вес ЭН

        if ($this->status and $p['point_a_locality_name'] . $p['point_a_locality_uuid'] . $p['point_a_locality_scoatou'] == '') {
            $this->status = false;
            $this->msg['code'] = 60403;
        } // Нет данных о населенном пункте отправки
        if ($this->status and $p['point_b_locality_name'] . $p['point_b_locality_uuid'] . $p['point_b_locality_scoatou'] == '') {
            $this->status = false;
            $this->msg['code'] = 60404;
        } // Нет данных о населенном пункте доставки

        $p['town_delivery'] = 1; // доставка по городу
        if ($this->status and $p['point_a_locality_name'] != $p['point_b_locality_name']) $p['town_delivery'] = 0;
        if ($this->status and $p['point_a_locality_uuid'] != $p['point_b_locality_uuid']) $p['town_delivery'] = 0;
        if ($this->status and $p['point_a_locality_scoatou'] != $p['point_b_locality_scoatou']) $p['town_delivery'] = 0;

        return $p;
    }

    private function setResult($data)
    {
        $this->result = $data;
        return $this->result;
    }

    private function calculate($p)
    {

        // если не указан типоразмер, то надо его определить
        if ($p['size'] == '') {
            $res = AtomAPIDB::query("
                SELECT 
                    * 
                FROM serviceapi_ew_size AS s
                ORDER BY s.weight_a, s.length_a
            ");
            while ($r = $res->fetch(PDO::FETCH_ASSOC)) {
                unset($r['id']);
                $sizes[] = $r;
            }

            $weight_k = -1; // ключ масива размеров, который подходит для веса
            $length_k = -1; // ключ масива размеров, который подходит для длины
            // оба отрицательные, так как ключ массива 0 существует

            foreach ($sizes as $k => $size) {

                // находим диапозоны к котороым относятся показатели посылки
                if ($p['weight'] >= $size['weight_a'] and $p['weight'] <= $size['weight_b']) {
                    $weight_k = $k;
                }
                if ($p['max_length'] >= $size['length_a'] and $p['max_length'] <= $size['length_b']) {
                    if ($length_k == -1) $length_k = $k; // необходимо найти только первое вхождение по дилне - TODO необходимо обосновать
                }

            }

            // проеряем результат поиска, мы должны найти ключ массива размеров как для веса так и для длины
            if ($weight_k < 0 or $length_k < 0) {
                $this->status = false;
                $this->msg['code'] = 60410;
            } // Не удалось обнаружить типпоразмер посылки для указанных параметров

            if ($this->status) {

                // типоразмер определяем по максимальному ключу
                $real_k = max($weight_k, $length_k);
                // сохраняем тоипоразмер
                $p['size'] = $sizes[$real_k]['name'];
            }

        }

        // находим стоимость
        $res = AtomAPIDB::query("
            SELECT 
                *
            FROM serviceapi_price_size AS ps
            WHERE 
                UPPER(ps.size) = UPPER('" . $p['size'] . "')
        ");
        $r = $res->fetch();

        $p['price'] = (float)$r['price_town'];
        if ($p['town_delivery'] == 0) (float)$p['price'] = $r['price_country'];

        if ($p['price'] == 0) {
            $this->status = false;
            $this->msg['code'] = 60420; // не удалось обнаружить тариф для указанных параметров
        } else {

            // если ставка есть, то делаем расчет страховки
            $p['price_insurance'] = $this->calculateInsurance($p);

            // если ставка есть, то делаем расчет COD
            $p['price_cod_commission'] = $this->calculateCOD($p);

            $p['price_total'] = $p['price'] + $p['price_insurance'] + $p['price_cod_commission'];

        }

        return $p;
    }

    private function calculateInsurance($p)
    {

        $res = AtomAPIDB::prepare("
            SELECT * FROM serviceapi_price_insurance WHERE
            point_a < :point_a AND point_b >= :point_b
            ORDER BY id DESC LIMIT 1
             
        ");
        $res->bindParam("point_a", $p['estcost']);
        $res->bindParam("point_b", $p['estcost']);
        $res->execute();
        $r = $res->fetch(PDO::FETCH_ASSOC);

        if ($r['id'] <= 0) {
            $insurance = 0;
        } else {

            if ($r['insurance_fix'] > 0) {
                $insurance = $r['insurance_fix'];
            } else {
                $insurance = $p['estcost'] * $r['insurance_proc'] / 100 + $r['insurance_over'];
                if ($r['insurance_min'] > 0) $insurance = min($insurance, $r['insurance_min']);
                if ($r['insurance_max'] > 0) $insurance = max($insurance, $r['insurance_max']);
            }

        }

        return round((float)$insurance, 2);
    }

    private function calculateCOD($p)
    {

        $res = AtomAPIDB::prepare("
            SELECT * FROM serviceapi_price_cod WHERE
            point_a < :point_a AND point_b >= :point_b
            ORDER BY id DESC LIMIT 1
             
        ");
        $res->bindParam("point_a", $p['cod']);
        $res->bindParam("point_b", $p['cod']);
        $res->execute();
        $r = $res->fetch(PDO::FETCH_ASSOC);

        if ($r['id'] <= 0) {
            $cod = 0;
        } else {

            if ($r['cod_fix'] > 0) {
                $cod = $r['cod_fix'];
            } else {
                $cod = $p['cod'] * $r['cod_proc'] / 100 + $r['cod_over'];
                if ($r['cod_min'] > 0) $cod = min($cod, $r['cod_min']);
                if ($r['cod_max'] > 0) $cod = max($cod, $r['cod_max']);
            }

        }

        return round((float)$cod, 2);
    }

    public function getResult()
    {
        return $this->result;
    }

}
