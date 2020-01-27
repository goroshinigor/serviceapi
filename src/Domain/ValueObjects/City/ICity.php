<?php

namespace App\Domain\ValueObjects\City;

/**
 * Description of City
 *
 * @author i.goroshyn
 */
interface ICity 
{
    public function equalsTo(ICity $iCity): bool;
    public function getUuid(): ?string;
    public function getScoatou(): ?string;
    public function getTitleRu(): ?string;
    public function getTitleUa(): ?string;
    public function getTitleEn(): ?string;
}
