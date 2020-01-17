<?php

namespace App\Bundles\JustinExceptionsBundle\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Bundles\JustinExceptionsBundle\Common\JustinExceptionResponseDTO;
use App\Bundles\JustinExceptionsBundle\Common\JustinExceptionResponseStatusDTO;
use App\Bundles\JustinExceptionsBundle\Common\JustinExceptionResponseResultDTO;
use App\Bundles\JustinExceptionsBundle\Common\JustinExceptionResponseMessageDTO;
use App\Bundles\JustinExceptionsBundle\Exception\LoginException;
use App\Bundles\JustinExceptionsBundle\Exception\SignException;
use App\Bundles\JustinExceptionsBundle\Exception\MethodException;

class JustinExceptionListener
{
    /**
     *
     * @var type 
     */
    private $requiredFields;
    
    /**
     * 
     * @param type $requiredFields
     */
    public function __construct($requiredFields) {
        $this->requiredFields = array_shift($requiredFields);
    }
    /**
     * 
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } elseif ($exception instanceof LoginException){
            $responseContent = $this->requiredFields['login'];
        } elseif ($exception instanceof SignException){
            $responseContent = $this->requiredFields['sign'];      
        } elseif ($exception instanceof MethodException){
            $responseContent = $this->requiredFields['method'];
        } elseif ($exception->getCode() == 60001){
            $responseContent = $this->requiredFields['method'];
        } else {
            $responseContent = [
                'ru'=>$exception->getMessage(),
                'ua'=>$exception->getMessage(),
                'en'=>$exception->getTraceAsString(),
                'code'=>$exception->getCode(),
            ];
        }

        $response = new JsonResponse(
            new JustinExceptionResponseDTO(
                new JustinExceptionResponseStatusDTO(false),
                new JustinExceptionResponseMessageDTO(
                    $responseContent['ru'],
                    $responseContent['ua'],
                    $responseContent['en'],
                    $responseContent['code']
                ),
                new JustinExceptionResponseResultDTO(null)
            )
        );
        
        $event->setResponse($response);
    }
}