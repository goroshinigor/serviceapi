<?php

class AtomAPIAccess
{

    public $status;
    public $msg;

    private $package;
    private $array;

    public function __construct($package)
    {

        $this->status = true;
        $this->package = $package;

        // Формируем масив из полученных данных
        $this->array = json_decode($this->package, true);

        // Вариант получения подписанного пакета
        if ($this->array['login'] != '' and $this->package) {

            $a = new AtomAPIUsers();
            $a->setFromDBByLogin($this->array['login']);
            if ($a->getUserAccessStatus() > 0) {

                if (strtolower($this->array['sign']) != AtomAPISign::getRealSign($this->package)) {
                    $this->status = false;
                    $this->msg['code'] = 60003; //
                }

            } else {

                $this->status = false;
                $this->msg['code'] = 60007;

            }

        } else {

            $this->status = false;
            $this->msg['code'] = 60002;

        }

    }

    public function getResult()
    {
        return null;
    }

}
