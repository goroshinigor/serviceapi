<?php

namespace App\Infrastructure\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceapiEwAllInfoBuffering
 *
 * @ORM\Table(name="serviceapi_ew_all_info_buffering")
 * @ORM\Entity
 */
class ServiceapiEwAllInfoBuffering
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
     * @ORM\Column(name="sender_uuid", type="string", length=70, nullable=false)
     */
    private $senderUuid;

    /**
     * @var string
     *
     * @ORM\Column(name="client_number", type="string", length=70, nullable=false)
     */
    private $clientNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="json_basic", type="text", length=65535, nullable=false)
     */
    private $jsonBasic;

    /**
     * @var string
     *
     * @ORM\Column(name="updatetime", type="string", length=255, nullable=false)
     */
    private $updatetime;


}
