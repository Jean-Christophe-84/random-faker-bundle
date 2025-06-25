<?php

namespace RandomFakerBundle\Attribute\Formatters\NumbersAndStrings;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerRandomFloat
{
    public function __construct(
        public ?int $nbMaxDecimals = null, public float $min = 0, public ?float $max = null
    ) {
    }
}
