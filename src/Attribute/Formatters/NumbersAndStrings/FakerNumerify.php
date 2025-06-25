<?php

namespace RandomFakerBundle\Attribute\Formatters\NumbersAndStrings;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerNumerify
{
    public function __construct(public string $string = '###')
    {
    }
}
