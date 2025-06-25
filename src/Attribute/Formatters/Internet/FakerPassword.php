<?php

namespace RandomFakerBundle\Attribute\Formatters\Internet;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerPassword
{
    public function __construct(public int $minLength = 6, public int $maxLength = 20)
    {
    }
}
