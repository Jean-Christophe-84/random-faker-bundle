<?php

namespace RandomFakerBundle\Attribute\Formatters\NumbersAndStrings;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerNumberBetween
{
    public function __construct(public int $int1, public int $int2)
    {
    }
}
