<?php

namespace App\Infrastructure\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceapiBranches
 *
 * @ORM\Table(name="serviceapi_branches")
 * @ORM\Entity
 */
class ServiceapiBranches
{
    /**
     * @var int
     *
     * @ORM\Column(name="number", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $number;

    /**
     * @var string|null
     *
     * @ORM\Column(name="adress", type="string", length=255, nullable=true)
     */
    private $adress;

    /**
     * @var string|null
     *
     * @ORM\Column(name="locality", type="string", length=100, nullable=true)
     */
    private $locality;

    /**
     * @var string|null
     *
     * @ORM\Column(name="type", type="string", length=200, nullable=true)
     */
    private $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="format", type="string", length=200, nullable=true)
     */
    private $format;

    /**
     * @var string|null
     *
     * @ORM\Column(name="delivery_branch_id", type="string", length=100, nullable=true)
     */
    private $deliveryBranchId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="max_weight", type="integer", nullable=true)
     */
    private $maxWeight;

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
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var string|null
     *
     * @ORM\Column(name="shedule_description", type="text", length=65535, nullable=true)
     */
    private $sheduleDescription;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photos", type="text", length=65535, nullable=true)
     */
    private $photos;

    /**
     * @var string|null
     *
     * @ORM\Column(name="services", type="text", length=65535, nullable=true)
     */
    private $services;

    /**
     * @var string|null
     *
     * @ORM\Column(name="public", type="text", length=65535, nullable=true)
     */
    private $public;

    /**
     * @var string|null
     *
     * @ORM\Column(name="updatetime", type="string", length=255, nullable=true)
     */
    private $updatetime;


}
