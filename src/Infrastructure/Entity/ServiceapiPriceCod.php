<?php

namespace App\Infrastructure\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceapiPriceCod
 *
 * @ORM\Table(name="serviceapi_price_cod")
 * @ORM\Entity
 */
class ServiceapiPriceCod
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
     * @ORM\Column(name="cod_proc", type="float", precision=5, scale=2, nullable=false)
     */
    private $codProc;

    /**
     * @var float
     *
     * @ORM\Column(name="cod_fix", type="float", precision=7, scale=2, nullable=false)
     */
    private $codFix;

    /**
     * @var float
     *
     * @ORM\Column(name="cod_min", type="float", precision=7, scale=2, nullable=false)
     */
    private $codMin;

    /**
     * @var float
     *
     * @ORM\Column(name="cod_max", type="float", precision=7, scale=2, nullable=false)
     */
    private $codMax;

    /**
     * @var float
     *
     * @ORM\Column(name="cod_over", type="float", precision=7, scale=2, nullable=false)
     */
    private $codOver;


}
