<?php

namespace RandomFakerBundle\Annotation\TextAndParagraphs;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerWords
{
    public $nb     = 3;

    public $asText = false;

    public function __construct(array $data)
    {
        $this->nb     = $data['nb'] ?? $this->nb;
        $this->asText = $data['as_text'] ?? $this->asText;
    }
}
