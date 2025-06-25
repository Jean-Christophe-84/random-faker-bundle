<?php

namespace RandomFakerBundle\Attribute\Formatters\TextAndParagraphs;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerParagraphs
{
    public function __construct(public int $nb = 3, public bool $asText = false)
    {
    }
}
