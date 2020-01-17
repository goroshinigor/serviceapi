<?php

namespace App\Domain\Aggregates;

interface ClientInterface {
    
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
