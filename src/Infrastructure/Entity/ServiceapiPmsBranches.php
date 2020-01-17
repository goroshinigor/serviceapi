<?php

namespace App\Infrastructure\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceapiPmsBranches
 *
 * @ORM\Table(name="serviceapi_pms_branches")
 * @ORM\Entity
 */
class ServiceapiPmsBranches
{
    /**
     * @var int
     *
     * @ORM\Column(name="int", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $int;

    /**
     * @var string
     *
     * @ORM\Column(name="filial_number", type="string", length=11, nullable=false)
     */
    private $filialNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="filial_uuid", type="string", length=255, nullable=false)
     */
    private $filialUuid;

    /**
     * @var string
     *
     * @ORM\Column(name="json_basic", type="text", length=65535, nullable=false)
     */
    private $jsonBasic;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_datetime", type="datetime", nullable=false)
     */
    private $updateDatetime;


}
