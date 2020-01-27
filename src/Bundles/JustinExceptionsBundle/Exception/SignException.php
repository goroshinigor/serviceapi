<?php

namespace App\Bundles\JustinExceptionsBundle\Exception;

use App\Bundles\JustinExceptionsBundle\Exception\IJustinException;

/**
 * Class SignException.
 */
class SignException extends \Exception implements IJustinException
{
    /**
     * RU_MESSAGE.
     */
    public const RU_MESSAGE = "Неправильная подпись пакета";

    /**
     * UA_MESSAGE.
     */
    public const UA_MESSAGE = "Неправильний підпис пакета";

    /**
     * EN_MESSAGE.
     */
    public const EN_MESSAGE = "Incorrect sign of package";
    
    /**
     * 
     * @return string
     */
    public function getMessageEn(): string 
    {
        return self::EN_MESSAGE;
    }

    /**
     * 
     * @return string
     */
    public function getMessageRu(): string 
    {
        return self::RU_MESSAGE;
    }

    /**
     * 
     * @return string
     */
    public function getMessageUa(): string 
    {
        return self::UA_MESSAGE;
    }

    /**
     * 
     * @param string $message
     * @param int $code
     * @param \Throwable $previous
     * @return \Exception
     */    
    
    public function __construct() {
        $message = self::RU_MESSAGE . '++' . self::UA_MESSAGE . '++' . self::EN_MESSAGE;
        throw new \Exception($message, 60003);
    }
}
