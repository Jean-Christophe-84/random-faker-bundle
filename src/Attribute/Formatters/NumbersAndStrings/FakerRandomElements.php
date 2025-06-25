<?php

namespace RandomFakerBundle\Attribute\Formatters\NumbersAndStrings;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerRandomElements
{
    public function __construct(
        public array $array = ['a', 'b', 'c'], public ?int $count = 1, public bool $max = false
    ) {
    }
}
