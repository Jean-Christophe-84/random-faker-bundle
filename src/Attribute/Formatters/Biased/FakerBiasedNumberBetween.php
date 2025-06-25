<?php

namespace RandomFakerBundle\Attribute\Formatters\Biased;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerBiasedNumberBetween
{
    public function __construct(public int $min = 0, public int $max = 100, public string $function = 'sqrt')
    {
    }
}
