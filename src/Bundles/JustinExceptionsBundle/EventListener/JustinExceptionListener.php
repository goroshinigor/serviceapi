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

        $event->allowCustomResponseCode();
        if ($exception instanceof HttpExceptionInterface) {
                $messages = $exception->getMessage();
                $message = array_shift($messages);
                $response = new JsonResponse(
                    new JustinExceptionResponseDTO(
                        new JustinExceptionResponseStatusDTO(false),
                        new JustinExceptionResponseMessageDTO($message, $message, $message, 0000),
                        new JustinExceptionResponseResultDTO([$trace])
                    )
                );
            
//            $response->setStatusCode($exception->getStatusCode());
//            $response->headers->replace($exception->getHeaders());
        } elseif ($exception instanceof LoginException){
            $responseContent = $this->requiredFields['login'];
        } elseif ($exception instanceof SignException){
            $responseContent = $this->requiredFields['sign'];
        } elseif ($exception instanceof MethodException){
            $responseContent = $this->requiredFields['method'];
        } elseif ($exception->getCode() == 60001){
            $responseContent = $this->requiredFields['method'];
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
        } else {
            $messages = $exception->getMessage();
            $trace = $exception->getTraceAsString();
            $messages = explode('++', $messages);

            //check if Exception have 3 translations
            if (3 == count($messages)) {
                $responseContent = [
                    'ru' => $messages[0],
                    'ua' => $messages[1],
                    'en' => $messages[2],
                    'code' => $exception->getCode(),
                ];
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
                ,200);
            } else {
                $message = array_shift($messages);
                $response = new JsonResponse(
                    new JustinExceptionResponseDTO(
                        new JustinExceptionResponseStatusDTO(false),
                        new JustinExceptionResponseMessageDTO($message, $message, $message, 0000),
                        new JustinExceptionResponseResultDTO([$trace])
                    )
                );
            }
        }
        $event->setResponse($response);
    }
}