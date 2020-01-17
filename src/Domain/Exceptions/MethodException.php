<?php

namespace App\Domain\Exceptions;

/**
 * Class MethodException.
 */
class MethodException extends \Exception
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
}
