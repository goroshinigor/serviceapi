<?php

namespace App\Infrastructure\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceapiAttikaFilialServices
 *
 * @ORM\Table(name="serviceapi_attika_filial_services")
 * @ORM\Entity
 */
class ServiceapiAttikaFilialServices
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="json", type="text", length=65535, nullable=false)
     */
    private $json;


}
