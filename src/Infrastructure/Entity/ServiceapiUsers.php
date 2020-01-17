<?php

namespace App\Infrastructure\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceapiUsers
 *
 * @ORM\Table(name="serviceapi_users")
 * @ORM\Entity
 */
class ServiceapiUsers
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
     * @ORM\Column(name="name", type="text", length=65535, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="login", type="text", length=65535, nullable=false)
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="key", type="text", length=65535, nullable=false)
     */
    private $key;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="access_end_date", type="date", nullable=false)
     */
    private $accessEndDate;

    /**
     * @var int
     *
     * @ORM\Column(name="is_test_mode", type="integer", nullable=false)
     */
    private $isTestMode;

    /**
     * @var int
     *
     * @ORM\Column(name="is_disable", type="integer", nullable=false)
     */
    private $isDisable;

    /**
     * @var int
     *
     * @ORM\Column(name="is_deleted", type="integer", nullable=false)
     */
    private $isDeleted;


}
