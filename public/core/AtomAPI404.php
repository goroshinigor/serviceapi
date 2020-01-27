<?php

class AtomAPI404
{

    public $status;
    public $msg;

    public function __construct()
    {
        $this->status = false;
        $this->msg['code'] = 60001;
    }

    public function getResult()
    {
        return null;
    }

}
