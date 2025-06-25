<?php

namespace RandomFakerBundle\Annotation\AvailableFormatters\Text;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerRealText
{
    public $maxNbChars = 200;

    public $indexSize  = 2;

    public function __construct(array $data)
    {
        $this->maxNbChars = $data['maxNbChars'] ?? $this->maxNbChars;
        $this->indexSize  = $data['indexSize'] ?? $this->indexSize;
    }
}
