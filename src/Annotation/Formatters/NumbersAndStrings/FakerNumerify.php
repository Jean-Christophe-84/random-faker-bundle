<?php

namespace RandomFakerBundle\Annotation\Formatters\NumbersAndStrings;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerNumerify
{
    public string $string = '###';

    public function __construct(array $data)
    {
        $this->string = $data['string'] ?? $this->string;
    }
}
