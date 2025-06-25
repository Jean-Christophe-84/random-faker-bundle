<?php

namespace RandomFakerBundle\Attribute\AvailableFormatters\Address;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerLongitude
{
    public function __construct(public float $min = -180, public float $max = 180)
    {
    }
}
