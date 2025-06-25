<?php

namespace RandomFakerBundle\Attribute\Formatters\Miscellaneous;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerBoolean
{
    public function __construct(public int $chanceOfGettingTrue = 50)
    {
    }
}
