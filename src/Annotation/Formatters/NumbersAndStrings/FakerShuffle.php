<?php

namespace RandomFakerBundle\Annotation\Formatters\NumbersAndStrings;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerShuffle
{
    public string $arg = '';

    public function __construct(array $data)
    {
        $this->arg = $data['arg'] ?? $this->arg;
    }
}
