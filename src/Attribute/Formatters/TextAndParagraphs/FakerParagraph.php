<?php

namespace RandomFakerBundle\Attribute\Formatters\TextAndParagraphs;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerParagraph
{
    public function __construct(
        public int $nbSentences = 3, public bool $variableNbSentences = true
    ) {
    }
}
