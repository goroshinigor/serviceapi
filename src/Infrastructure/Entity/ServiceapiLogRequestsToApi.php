<?php

namespace App\Infrastructure\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceapiLogRequestsToApi
 *
 * @ORM\Table(name="serviceapi_log_requests_to_api", indexes={@ORM\Index(name="login", columns={"login"}), @ORM\Index(name="session_uuid", columns={"session_uuid"}), @ORM\Index(name="msg_code", columns={"msg_code"})})
 * @ORM\Entity
 */
class ServiceapiLogRequestsToApi
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="login", type="string", length=255, nullable=false)
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="session_uuid", type="string", length=255, nullable=false)
     */
    private $sessionUuid;

    /**
     * @var string
     *
     * @ORM\Column(name="request_url", type="text", length=65535, nullable=false)
     */
    private $requestUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="request_data", type="text", length=65535, nullable=false)
     */
    private $requestData;

    /**
     * @var string
     *
     * @ORM\Column(name="request_headers", type="text", length=65535, nullable=false)
     */
    private $requestHeaders;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="request_datetime", type="datetime", nullable=false)
     */
    private $requestDatetime;

    /**
     * @var string
     *
     * @ORM\Column(name="request_remote_ip", type="text", length=65535, nullable=false)
     */
    private $requestRemoteIp;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status;

    /**
     * @var int
     *
     * @ORM\Column(name="msg_code", type="integer", nullable=false)
     */
    private $msgCode;

    /**
     * @var string
     *
     * @ORM\Column(name="result_data", type="text", length=65535, nullable=false)
     */
    private $resultData;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="result_datetime", type="datetime", nullable=false)
     */
    private $resultDatetime;


}
