<?php

namespace RandomFakerBundle\Annotation\Formatters\Internet;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerPassword
{
    public $minLength = 6;

    public $maxLength = 20;

    public function __construct(array $data)
    {
        $this->minLength = $data['minLength'] ?? $this->minLength;
        $this->maxLength = $data['maxLength'] ?? $this->maxLength;
    }
}
