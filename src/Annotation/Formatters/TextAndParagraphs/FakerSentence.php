<?php

namespace RandomFakerBundle\Annotation\TextAndParagraphs;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerSentence
{
    public $nbWords         = 6;

    public $variableNbWords = true;

    public function __construct(array $data)
    {
        $this->nbWords         = $data['nbWords'] ?? $this->nbWords;
        $this->variableNbWords = $data['variableNbWords'] ?? $this->variableNbWords;
    }
}
