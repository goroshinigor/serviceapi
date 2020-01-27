<?php

namespace App\Infrastructure\Services\Common;

use Symfony\Component\HttpFoundation\Request;

/**
 * MethodNameFromRequest.
 */
class MethodNameFromRequest {

    /**
     * 
     * @param \App\Infrastructure\Services\Common\Request $request
     * @return boolean
     */
    public function get(Request $request): string
    {
        return json_decode($request->getContent())->method;
    }
}
