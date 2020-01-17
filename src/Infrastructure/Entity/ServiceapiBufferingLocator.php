<?php

namespace App\Infrastructure\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceapiBufferingLocator
 *
 * @ORM\Table(name="serviceapi_buffering_locator")
 * @ORM\Entity
 */
class ServiceapiBufferingLocator
{
    /**
     * @var int
     *
     * @ORM\Column(name="forming_number", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $formingNumber;

    /**
     * @var string|null
     *
     * @ORM\Column(name="lat", type="string", length=255, nullable=true)
     */
    private $lat;

    /**
     * @var string|null
     *
     * @ORM\Column(name="lng", type="string", length=255, nullable=true)
     */
    private $lng;

    /**
     * @var string|null
     *
     * @ORM\Column(name="buffering_time", type="string", length=255, nullable=true)
     */
    private $bufferingTime;


}
