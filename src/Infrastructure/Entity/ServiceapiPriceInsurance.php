<?php

namespace App\Infrastructure\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceapiPriceInsurance
 *
 * @ORM\Table(name="serviceapi_price_insurance")
 * @ORM\Entity
 */
class ServiceapiPriceInsurance
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
     * @var float
     *
     * @ORM\Column(name="point_a", type="float", precision=8, scale=2, nullable=false)
     */
    private $pointA;

    /**
     * @var float
     *
     * @ORM\Column(name="point_b", type="float", precision=8, scale=2, nullable=false)
     */
    private $pointB;

    /**
     * @var float
     *
     * @ORM\Column(name="insurance_proc", type="float", precision=5, scale=2, nullable=false)
     */
    private $insuranceProc;

    /**
     * @var float
     *
     * @ORM\Column(name="insurance_fix", type="float", precision=7, scale=2, nullable=false)
     */
    private $insuranceFix;

    /**
     * @var float
     *
     * @ORM\Column(name="insurance_min", type="float", precision=7, scale=2, nullable=false)
     */
    private $insuranceMin;

    /**
     * @var float
     *
     * @ORM\Column(name="insurance_max", type="float", precision=7, scale=2, nullable=false)
     */
    private $insuranceMax;

    /**
     * @var float
     *
     * @ORM\Column(name="insurance_over", type="float", precision=7, scale=2, nullable=false)
     */
    private $insuranceOver;


}
