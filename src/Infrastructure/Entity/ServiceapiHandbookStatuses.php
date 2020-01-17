<?php

namespace App\Infrastructure\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceapiHandbookStatuses
 *
 * @ORM\Table(name="serviceapi_handbook_statuses")
 * @ORM\Entity
 */
class ServiceapiHandbookStatuses
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
     * @var string|null
     *
     * @ORM\Column(name="uuid", type="string", length=200, nullable=true)
     */
    private $uuid;

    /**
     * @var string|null
     *
     * @ORM\Column(name="code", type="string", length=200, nullable=true)
     */
    private $code;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="is_archive", type="integer", nullable=false, options={"comment"="1 - archive, 0 - not"})
     */
    private $isArchive;

    /**
     * @var string|null
     *
     * @ORM\Column(name="updatetime", type="string", length=255, nullable=true)
     */
    private $updatetime;


}
