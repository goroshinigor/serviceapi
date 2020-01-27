<?php

namespace App\Infrastructure\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceapiAttikaFilials
 *
 * @ORM\Table(name="serviceapi_attika_filials")
 * @ORM\Entity
 */
class ServiceapiAttikaFilials
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
     * @ORM\Column(name="json_basic", type="text", length=65535, nullable=false)
     */
    private $jsonBasic;

    /**
     * @var string
     *
     * @ORM\Column(name="json_services", type="text", length=65535, nullable=false)
     */
    private $jsonServices;

    /**
     * @var string
     *
     * @ORM\Column(name="json_public", type="text", length=65535, nullable=false)
     */
    private $jsonPublic;

    /**
     * @var string
     *
     * @ORM\Column(name="json_photos", type="text", length=65535, nullable=false)
     */
    private $jsonPhotos;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_datetime", type="datetime", nullable=false)
     */
    private $updateDatetime;


}
