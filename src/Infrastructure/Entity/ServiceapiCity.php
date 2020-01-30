<?php

namespace App\Infrastructure\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Domain\ValueObjects\City\ICity;

/**
 * @ORM\Entity(repositoryClass="App\Infrastructure\Repository\ServiceapiCityRepository")
 * @ORM\Table(name="serviceapi_cities")
 */
class ServiceapiCity implements ICity
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
    private $uuid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $scoatou;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title_ua;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title_ru;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title_en;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $region_uuid;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @var ServiceapiRegion|null
     *
     */
    private $region;

    public function getRegion(): ServiceapiRegion
    {
        return $this->region;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getScoatou(): ?string
    {
        return $this->scoatou;
    }

    public function setScoatou(string $scoatou): self
    {
        $this->scoatou = $scoatou;

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

    public function getTitleRu(): ?string
    {
        return $this->title_ru;
    }

    public function setTitleRu(string $title_ru): self
    {
        $this->title_ru = $title_ru;

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

    public function getRegionUuid(): ?string
    {
        return $this->region_uuid;
    }

    public function setRegionUuid(string $regionUuid): self
    {
        $this->region_uuid = $regionUuid;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Comparison function.
     */
    public function equalsTo(ICity $iCity): bool
    {
        return (bool)($iCity->getUuid() == $this->getUuid());
    }
}
