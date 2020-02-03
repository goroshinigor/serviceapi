<?php

namespace App\Infrastructure\Services\Remote\PMSIntegration\Authorization;

/**
 * Description of GenerateSignFromPassword
 *
 * @author i.goroshyn
 */
class GenerateSignFromPassword {

    /**
     * 
     * @param type $password
     * @return type
     */
    public function generate($password)
    {
        return sha1($password . ':' . date('Y-m-d'));
    }
}
