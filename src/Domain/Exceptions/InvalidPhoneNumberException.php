<?php

namespace App\Domain\Exceptions;
/*
 * AddressNotFoundException
 */

use Throwable;

class InvalidPhoneNumberException extends \Exception
{
    public function __construct($message = "", $code = 60201, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * RU_MESSAGE.
     */
    public const RU_MESSAGE = "Указанный телефон не соответствует формату +380999999999";

    /**
     * UA_MESSAGE.
     */
    public const UA_MESSAGE = "Зазначений телефон не відповідає формату +380999999999";

    /**
     * EN_MESSAGE.
     */
    public const EN_MESSAGE = "The specified phone does not match the format +380999999999";

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