<?php

namespace RandomFakerBundle\Annotation\Formatters\NumbersAndStrings;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerRandomNumber
{
    public ?int $nbDigits = null;

    public bool $strict   = false;

    public function __construct(array $data)
    {
        $this->nbDigits = $data['nbDigits'] ?? $this->nbDigits;
        $this->strict   = $data['strict'] ?? $this->strict;
    }
}
