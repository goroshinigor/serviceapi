<?php

namespace App\Infrastructure\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceapiPriceSize
 *
 * @ORM\Table(name="serviceapi_price_size")
 * @ORM\Entity
 */
class ServiceapiPriceSize
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
     * @var int
     *
     * @ORM\Column(name="parent_id", type="integer", nullable=false)
     */
    private $parentId;

    /**
     * @var string
     *
     * @ORM\Column(name="size", type="string", length=10, nullable=false)
     */
    private $size;

    /**
     * @var string
     *
     * @ORM\Column(name="price_town", type="decimal", precision=11, scale=2, nullable=false)
     */
    private $priceTown;

    /**
     * @var string|null
     *
     * @ORM\Column(name="price_country", type="decimal", precision=11, scale=2, nullable=true)
     */
    private $priceCountry;


}
