<?php

namespace RandomFakerBundle\Annotation\Formatters\TextAndParagraphs;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerText
{
    public $maxNbChars = 200;

    public function __construct(array $data)
    {
        $this->maxNbChars = $data['maxNbChars'] ?? $this->maxNbChars;
    }
}
