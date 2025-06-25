<?php

namespace RandomFakerBundle\Attribute\Formatters\TextAndParagraphs;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerText
{
    public function __construct(public int $maxNbChars = 200)
    {
    }
}
