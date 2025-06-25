<?php

namespace RandomFakerBundle\Annotation\Formatters\HTMLLorem;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerRandomHtml
{
    public $maxDepth = 4;

    public $maxWidth = 4;

    public function __construct(array $data)
    {
        $this->maxDepth = $data['maxDepth'] ?? $this->maxDepth;
        $this->maxWidth = $data['maxWidth'] ?? $this->maxWidth;
    }
}
