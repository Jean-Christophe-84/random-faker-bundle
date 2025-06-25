<?php

namespace RandomFakerBundle\Attribute\Formatters\Internet;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerSlug
{
    public function __construct(public int $nbWords = 6, public bool $variableNbWords = true)
    {
    }
}
