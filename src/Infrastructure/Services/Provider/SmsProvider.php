<?php

namespace App\Infrastructure\Services\Provider;

use App\Infrastructure\Services\Provider\SMSProvider\ISmsProvider;
use App\Infrastructure\Services\Provider\SMSProvider\VostokSmsProvider;

class SmsProvider implements IProvider
{
    /**
     * @var ISmsProvider
     */
    private $provider;
    /**
     * @var string Path for SMS Providers
     */
    private $providerPath = '%s\%sSmsProvider';

    public function getProvider(): ISmsProvider
    {
        if (isset($_ENV['SMS_PROVIDER'])) {
            $providerName = sprintf($this->providerPath, $_ENV['SMS_PROVIDERS_PATH'], $_ENV['SMS_PROVIDER']);
            if ((class_exists($providerName)) && ($provider = new $providerName()) instanceof ISmsProvider) {
                $this->provider = $provider;
            }
        }
        return $this->provider ?? new VostokSmsProvider();
    }
}