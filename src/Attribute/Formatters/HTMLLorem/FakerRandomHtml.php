<?php

namespace RandomFakerBundle\Attribute\Formatters\HTMLLorem;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerRandomHtml
{
    public function __construct(public int $maxDepth = 4, public int $maxWidth = 4)
    {
    }
}
