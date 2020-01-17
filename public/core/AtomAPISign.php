<?php

class AtomAPISign
{

    private function __construct()
    {
    }

    public static function createFull($data)
    {

        $a = new AtomAPIUsers();
        $a->setFromDBByLogin($data['login']);
        $key = $a->getKey();

        $data['sign'] = '';

        $string = json_encode($data) . $key;
        $sign = sha1($string, true);
        $sign = bin2hex($sign);

        $data['sign'] = $sign;
        $string = json_encode($data);

        return $string;

    }

    public static function getRealSign($package)
    {

        $array = json_decode($package, true);

        $a = new AtomAPIUsers();
        $a->setFromDBByLogin($array['login']);
        $key = $a->getKey();

        $package_without_sign = str_replace($array['sign'], '', $package);
        $sign = sha1($package_without_sign . $key, true);
        $sign = bin2hex($sign);

        return $sign;
    }


    private function __clone()
    {
    }

}
