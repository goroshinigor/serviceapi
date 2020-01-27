<?php


namespace App\Infrastructure\Services\Legacy;

use App\Infrastructure\Services\Legacy\AtomApiDb;
use App\Infrastructure\Services\Legacy\AtomWee;

class AtomApiUsers
{

    public $msg;
    public $status = true;

    private $attr;


    // Инициализация объекта

    public function setFromDBByLogin($login)
    {
        $this->attr['login'] = (string)$login;

        if (!$this->status) return $this->status;

        $res = AtomAPIDB::query("SELECT * FROM serviceapi_users WHERE login = '" . $this->attr['login'] . "' ");
        $data = $res->fetch();
        $this->set($data);

        return $this->status;
    }

    // Инициализация объекта из БД

    public function set($data)
    {
        $this->attr['id'] = (int)$data['id'];
        $this->attr['name'] = (string)$data['name'];
        $this->attr['login'] = (string)$data['login'];
        $this->attr['key'] = (string)$data['key'];
        $this->attr['access_end_date'] = AtomWee::dateConverter((string)$data['access_end_date'], 'iso_date');
        $this->attr['is_disable'] = (int)$data['is_disable'];
        $this->attr['is_test_mode'] = (int)$data['is_test_mode'];

        return $this->status;
    }


    public function getKey()
    {
        return $this->attr['key'];
    }


    public function getJSON()
    {
        return json_encode($this->getData());
    }


    public function getData()
    {

        if ($this->attr['access_end_date']) $this->attr['access_end_date'] = AtomWee::dateConverter($this->attr['access_end_date'], 'ua_date');
        return $this->attr;

    }


    public function getUserAccessStatus()
    {
        if ($this->attr['id'] > 0 and $this->attr['is_disabled'] == 0) {
            return 1;
        }
        return 0;
    }

}
