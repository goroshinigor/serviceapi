<?php

namespace App\Infrastructure\Services\Provider\SMSProvider;

interface ISmsProvider
{
    public function send($contactId, ISmsProviderParameters $providerParameters);
}