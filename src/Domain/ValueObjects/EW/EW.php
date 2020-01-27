<?php

namespace App\Domain\ValueObjects\EW;

use App\Domain\ValueObjects\EW\Size\ISize;
/**
 * Class EW ValueObject
 */
class EW 
{
    /**
     *
     * @var type IEwSize
     */
    private $size;

    /**
     * 
     * @param IEwSize $size
     */
    public function __construct(ISize $size) {
        $this->size = $size;
    }
}
