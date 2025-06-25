<?php

namespace RandomFakerBundle\Attribute\AvailableFormatters\Address;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerLatitude
{
    public function __construct(public float $min = -90, public float $max = 90)
    {
    }
}
