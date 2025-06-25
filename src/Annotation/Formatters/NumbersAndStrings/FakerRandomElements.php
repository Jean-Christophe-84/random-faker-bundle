<?php

namespace RandomFakerBundle\Annotation\Formatters\NumbersAndStrings;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerRandomElements
{
    public $array = ['a', 'b', 'c'];

    public $count = 1;

    public $max   = false;

    public function __construct(array $data)
    {
        $this->array = $data['array'] ?? $this->array;
        $this->count = $data['count'] ?? $this->count;
        $this->max   = $data['max'] ?? $this->max;
    }
}
