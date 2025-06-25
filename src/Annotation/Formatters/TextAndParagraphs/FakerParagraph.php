<?php

namespace RandomFakerBundle\Annotation\TextAndParagraphs;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerParagraph
{
    public int  $nbSentences         = 3;

    public bool $variableNbSentences = true;

    public function __construct(array $data)
    {
        $this->nbSentences         = $data['nbSentences'] ?? $this->nbSentences;
        $this->variableNbSentences = $data['variableNbSentences'] ?? $this->variableNbSentences;
    }
}
