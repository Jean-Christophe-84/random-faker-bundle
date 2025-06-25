<?php

namespace RandomFakerBundle\Annotation\Formatters\NumbersAndStrings;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerRandomElement
{
    public $array = ['a', 'b', 'c'];

    public function __construct(array $data)
    {
        $this->array = $data['array'] ?? $this->array;
    }
}
