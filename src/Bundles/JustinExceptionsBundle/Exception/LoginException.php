<?php

namespace App\Bundles\JustinExceptionsBundle\Exception;

use App\Bundles\JustinExceptionsBundle\Exception\IJustinException;

/**
 * Class LoginException.
 */
class LoginException extends \Exception implements IJustinException
{
    /**
     * RU_MESSAGE.
     */
    public const RU_MESSAGE = "Пользователь не обнаружен в Базе";

    /**
     * UA_MESSAGE.
     */
    public const UA_MESSAGE = "Користувач не виявлений в Базі";

    /**
     * EN_MESSAGE.
     */
    public const EN_MESSAGE = "User not found in DB";

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
