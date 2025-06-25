<?php

namespace RandomFakerBundle\Attribute\Formatters\TextAndParagraphs;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerSentence
{
    public function __construct(public int $nbWords = 6, public bool $variableNbWords = true)
    {
    }
}
