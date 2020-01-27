<?php

namespace App\Infrastructure\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceapiEwInfoBasicBuffering
 *
 * @ORM\Table(name="serviceapi_ew_info_basic_buffering")
 * @ORM\Entity
 */
class ServiceapiEwInfoBasicBuffering
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
     * @ORM\Column(name="phone", type="string", length=50, nullable=false)
     */
    private $phone;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_from", type="date", nullable=false)
     */
    private $dateFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_to", type="date", nullable=false)
     */
    private $dateTo;

    /**
     * @var int
     *
     * @ORM\Column(name="command", type="integer", nullable=false)
     */
    private $command;

    /**
     * @var string
     *
     * @ORM\Column(name="updatetime", type="string", length=255, nullable=false)
     */
    private $updatetime;


}
