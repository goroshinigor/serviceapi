<?php

namespace App\Infrastructure\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceapiBranch
 *
 * @ORM\Table(name="serviceapi_branches")
 * @ORM\Entity(repositoryClass="App\Infrastructure\Repository\ServiceapiBranchRepository")
 */
class ServiceapiBranch
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="number", type="string", length=255, nullable=false)
     */
    private $number;

    /**
     * @var string|null
     *
     * @ORM\Column(name="adress", type="string", length=255, nullable=true)
     */
    private $adress;

    /**
     * @var string|null
     *
     * @ORM\Column(name="locality", type="string", length=100, nullable=true)
     */
    private $locality;

    /**
     * @var string|null
     *
     * @ORM\Column(name="type", type="string", length=200, nullable=true)
     */
    private $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="format", type="string", length=200, nullable=true)
     */
    private $format;

    /**
     * @var string|null
     *
     * @ORM\Column(name="delivery_branch_id", type="string", length=100, nullable=true)
     */
    private $deliveryBranchId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="max_weight", type="integer", nullable=true)
     */
    private $maxWeight;

    /**
     * @var string|null
     *
     * @ORM\Column(name="lat", type="string", length=255, nullable=true)
     */
    private $lat;

    /**
     * @var string|null
     *
     * @ORM\Column(name="lng", type="string", length=255, nullable=true)
     */
    private $lng;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var string|null
     *
     * @ORM\Column(name="shedule_description", type="text", length=65535, nullable=true)
     */
    private $sheduleDescription;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photos", type="text", length=65535, nullable=true)
     */
    private $photos;

    /**
     * @var string|null
     *
     * @ORM\Column(name="services", type="text", length=65535, nullable=true)
     */
    private $services;

    /**
     * @var string|null
     *
     * @ORM\Column(name="public", type="text", length=65535, nullable=true)
     */
    private $public;

    /**
     * @var string|null
     *
     * @ORM\Column(name="updatetime", type="string", length=255, nullable=true)
     */
    private $updatetime;

    function getNumber() {
        return $this->number;
    }

    function getAdress() {
        return $this->adress;
    }

    function getLocality() {
        return $this->locality;
    }

    function getType() {
        return $this->type;
    }

    function getFormat() {
        return $this->format;
    }

    function getDeliveryBranchId() {
        return $this->deliveryBranchId;
    }

    function getMaxWeight() {
        return $this->maxWeight;
    }

    function getLat() {
        return $this->lat;
    }

    function getLng() {
        return $this->lng;
    }

    function getDescription() {
        return $this->description;
    }

    function getSheduleDescription() {
        return $this->sheduleDescription;
    }

    function getPhotos() {
        return $this->photos;
    }

    function getServices() {
        return $this->services;
    }

    function getPublic() {
        return $this->public;
    }

    function getUpdatetime() {
        return $this->updatetime;
    }

    function setNumber($number) {
        $this->number = $number;

        return $this;
    }

    function setAdress($adress) {
        $this->adress = $adress;

        return $this;
    }

    function setLocality(string $locality) {
        $this->locality = $locality;

        return $this;
    }

    function setType(int $type) {
        $this->type = $type;

        return $this; 
    }

    function setFormat(string $format) {
        $this->format = $format;

        return $this;
    }

    function setDeliveryBranchId(int $deliveryBranchId) {
        $this->deliveryBranchId = $deliveryBranchId;

        return $this;
    }

    function setMaxWeight(int $maxWeight) {
        $this->maxWeight = $maxWeight;

        return $this;
    }

    function setLat(float $lat) {
        $this->lat = $lat;

        return $this;
    }

    function setLng(float $lng) {
        $this->lng = $lng;

        return $this;
    }

    function setDescription(string $description) {
        $this->description = $description;

        return $this;
    }

    function setSheduleDescription(string $sheduleDescription) {
        $this->sheduleDescription = $sheduleDescription;

        return $this;
    }

    function setPhotos(string $photos) {
        $this->photos = $photos;

        return $this;
    }

    function setServices(string $services) {
        $this->services = $services;

        return $this;
    }

    function setPublic(string $public) {
        $this->public = $public;

        return $this;
    }

    function setUpdatetime(int $updatetime) {
        $this->updatetime = $updatetime;

        return $this;
    }
}
