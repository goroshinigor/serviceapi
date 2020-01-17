<?php

class ServiceAPIFilialsList
{

    public $status;
    public $msg;

    private $result;

    function __construct()
    {

        $this->status = true;

        $a = new ServiceAPIJustinBranches();
        $filials = $a->getBranches();

        $b = new AttikaAPIFilials();
        $filials_attika = $b->getFilials();

        foreach ($filials as $k => $v) {
            $filials[$k]['schedule'] = isset($filials_attika[$k]['basic']['schedule'])?$filials_attika[$k]['basic']['schedule']:'';

            $filials[$k]['photos'] = $filials_attika[$k]['photos'];
            $filials[$k]['services'] = $filials_attika[$k]['services'];
            $filials[$k]['public'] = $filials_attika[$k]['public'];


        }

        $res['filials'] = $filials;
        $res['available_services'] = $b->getFilialServices();

        $this->setResult($res);

    }

    private function setResult($data)
    {
        $this->result = $data;
        return $this->result;
    }


    public function getResult()
    {

        foreach ((array)$this->result as $k => $v) {
            $this->result[$k] = str_replace("0000-00-00 00:00:00", "", $this->result[$k]);
            $this->result[$k] = str_replace("0000-00-00", "", $this->result[$k]);
        }

        return $this->result;
    }


}