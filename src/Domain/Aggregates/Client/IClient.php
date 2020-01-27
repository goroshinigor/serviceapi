<?php

namespace App\Domain\Aggregates\Client;

interface IClient {

    /**
     * 
     */
    public function registration();

    /**
     * 
     */
    public function update();

    /**
     * 
     */
    public function delete();

    /**
     * 
     */
    public function info();

    /**
     * 
     */
    public function verifyPhone();

    /**
     * 
     */
    public function checkPhone();

    /**
     * 
     */
    public function loginPhone();
}
