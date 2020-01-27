<?php

namespace App\Infrastructure\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceapiEwSize
 *
 * @ORM\Table(name="serviceapi_ew_size")
 * @ORM\Entity
 */
class ServiceapiEwSize
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
     * @ORM\Column(name="name", type="string", length=20, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="weight_a", type="decimal", precision=11, scale=2, nullable=false)
     */
    private $weightA;

    /**
     * @var string
     *
     * @ORM\Column(name="weight_b", type="decimal", precision=11, scale=2, nullable=false)
     */
    private $weightB;

    /**
     * @var int
     *
     * @ORM\Column(name="length_a", type="integer", nullable=false)
     */
    private $lengthA;

    /**
     * @var int
     *
     * @ORM\Column(name="length_b", type="integer", nullable=false)
     */
    private $lengthB;


}
