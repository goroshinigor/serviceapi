<?php

namespace App\Bundles\JustinExceptionsBundle\Exception;

use App\Bundles\JustinExceptionsBundle\Exception\IJustinException;

/**
 * Class MethodException.
 */
class MethodException extends \Exception implements IJustinException
{
    /**
     * RU_MESSAGE.
     */
    public const RU_MESSAGE = "Не указан объект обработки и/или действие с объектом";

    /**
     * UA_MESSAGE.
     */
    public const UA_MESSAGE = "Не вказаний об'єкт обробки та / або дія з об'єктом";

    /**
     * EN_MESSAGE.
     */
    public const EN_MESSAGE = "Object and/or action not found";

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
        throw new \Exception($message, 60001, $previous);
    }
}
