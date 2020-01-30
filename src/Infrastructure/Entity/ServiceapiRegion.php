<?php

namespace App\Infrastructure\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Infrastructure\Repository\ServiceapiRegionRepository")
 * @ORM\Table(name="serviceapi_regions")
 */
class ServiceapiRegion
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title_ru;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title_ua;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title_en;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=36)
     */
    private $uuid;

    /**
     *  @var ServiceapiCity[]|null
     *
     */
    private $cities;

    public function getCities()
    {
        return $this->cities;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitleRu(): ?string
    {
        return $this->title_ru;
    }

    public function setTitleRu(string $title_ru): self
    {
        $this->title_ru = $title_ru;

        return $this;
    }

    public function getTitleUa(): ?string
    {
        return $this->title_ua;
    }

    public function setTitleUa(string $title_ua): self
    {
        $this->title_ua = $title_ua;

        return $this;
    }

    public function getTitleEn(): ?string
    {
        return $this->title_en;
    }

    public function setTitleEn(string $title_en): self
    {
        $this->title_en = $title_en;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }
}
