<?php

namespace App\Bundles\JustinExceptionsBundle\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use App\Bundles\JustinExceptionsBundle\Exception\LoginException;
use App\Bundles\JustinExceptionsBundle\Exception\SignException;
use App\Bundles\JustinExceptionsBundle\Exception\MethodException;

/**
 * 
 */
class JustinRequestListener {

    /**
     * 
     * @param ExceptionEvent $event
     */
    public function onKernelRequest(RequestEvent  $event)
    {
        try{
            $data = json_decode($event->getRequest()->getContent());
            if ($data == null) throw new \Exception('Не валидный json++Не валідний json++Invalid json',00000);
            if(!isset($data->login) || empty($data->login)){
                throw new LoginException();
            } elseif(!isset($data->sign) || empty($data->sign)){
                throw new SignException();
            } elseif(!isset($data->method) || empty($data->method)) {
                throw new MethodException();
            }
        } catch (Exception $ex) {
            throw new \Exception($ex->getMessage(),$ex->getCode());
        }        
    }
}
