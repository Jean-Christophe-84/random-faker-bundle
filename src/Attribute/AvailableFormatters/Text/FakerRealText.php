<?php

namespace RandomFakerBundle\Attribute\AvailableFormatters\Text;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerRealText
{
    public function __construct(public float $maxNbChars = 200, public float $indexSize = 2)
    {
    }
}
