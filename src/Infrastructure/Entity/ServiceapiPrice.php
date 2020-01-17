<?php

namespace App\Infrastructure\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceapiPrice
 *
 * @ORM\Table(name="serviceapi_price")
 * @ORM\Entity
 */
class ServiceapiPrice
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
     * @ORM\Column(name="memberId", type="string", length=9, nullable=false)
     */
    private $memberid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetime_start", type="datetime", nullable=false)
     */
    private $datetimeStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetime_end", type="datetime", nullable=false)
     */
    private $datetimeEnd;

    /**
     * @var int
     *
     * @ORM\Column(name="is_active", type="integer", nullable=false)
     */
    private $isActive;


}
