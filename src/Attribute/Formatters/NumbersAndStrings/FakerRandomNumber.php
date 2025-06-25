<?php

namespace RandomFakerBundle\Attribute\Formatters\NumbersAndStrings;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerRandomNumber
{
    public function __construct(public ?int $nbDigits = null, public bool $strict = false)
    {
    }
}
