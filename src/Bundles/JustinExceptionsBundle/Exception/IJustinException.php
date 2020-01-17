<?php

namespace App\Bundles\JustinExceptionsBundle\Exception;

/**
 *
 * @author i.goroshyn
 */
interface IJustinException {
    public function getMessageRu():string;
    public function getMessageUa():string;
    public function getMessageEn():string;
}
