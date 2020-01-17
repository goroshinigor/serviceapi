<?php

namespace App\Infrastructure\Services\Provider\SMSProvider;

use GuzzleHttp\Client;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use DateTime;
use Exception;

class VostokSmsProvider implements ISmsProvider
{
    private const CACHE_PREFIX = 'vostok';
    /**
     * @var string VostokSMS token
     */
    private $token;
    /**
     * @var Client
     */
    private $client;
    /**
     * @var string VostokSMSGate url
     */
    private $apiUrl;
    /**
     * @var RedisAdapter
     */
    private $cache;

    public function __construct()
    {
        $this->cache = new RedisAdapter(RedisAdapter::createConnection('redis://localhost'), self::CACHE_PREFIX);
        $this->apiUrl = $_ENV['VOSTOK_API_URL'];
        $user = $_ENV['VOSTOK_API_USER'];
        $password = $_ENV['VOSTOK_API_PASSWORD'];
        $this->client = new Client(['curl' => array(CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => false),]);
        $this->setToken($user, $password);
    }

    /**
     * @param string $user Vostok user
     * @param string $password Vostok password
     * @throws Exception
     */
    private function setToken(string $user, string $password)
    {
        $cacheItem = $this->cache->getItem(self::CACHE_PREFIX . '_token');
        if ($cacheItem->isHit()) {
            $this->token = $cacheItem->get();
            return;
        }
        $data = [
            'client' => $user,
            'password' => $password,
        ];
        $response = $this->client->post($this->apiUrl . '/token', [
            'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
            'body' => json_encode($data)
        ]);
        if (200 == $response->getStatusCode() && $data = json_decode($response->getBody())) {
            $this->token = $data->accessToken;
            $cacheItem->set($this->token);
            $cacheItem->expiresAt(new DateTime($data->expires));
            $this->cache->save($cacheItem);
        } else {
            throw new Exception('Wrong Vostok token');
        }
    }

    /**
     * @param $contactId int
     * @param ISmsProviderParameters $providerParameters
     * @return mixed
     * @throws Exception
     */
    public function send($contactId, ISmsProviderParameters $providerParameters)
    {
        $responce = $this->sendMessage($providerParameters->phoneNumber, $providerParameters->text);
        $responce->data = $providerParameters->text;
        return $responce;
    }

    /**
     * @param string $recipient sms recipient
     * @param string $message message content
     * @return mixed
     * @throws Exception
     */
    private function sendMessage(string $recipient, string $message)
    {
        $data = $this->sendMessagePattern($recipient, $message);
        $response = $this->client->post($this->apiUrl . '/message', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
            ],
            'body' => json_encode($data)
        ]);
        if (200 == $response->getStatusCode()) {
            $response = json_decode($response->getBody());
            if ($response && 1 == $response->status->statusId) {
                $response->resultCode = 1;
                return $response;
            }
        }
        throw new Exception('Wrong provider response');
    }

    /**
     * @param string $recipient
     * @param string $message
     * @return array
     */
    private function sendMessagePattern(string $recipient, string $message)
    {
        return [
            'recipient' => $recipient,
            'externalId' => 'string',
            'isPromotional' => false,
            'channel' => [
                'sms' => [
                    'text' => $message,
                    'ttl' => 0,
                    'isTranslit' => false
                ]
            ]
        ];
    }
}