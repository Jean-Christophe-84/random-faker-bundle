<?php

namespace RandomFakerBundle\Annotation\Formatters\NumbersAndStrings;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerNumberBetween
{
    public $int1 = 0;

    public $int2 = 2147483647;

    public function __construct(array $data)
    {
        $this->int1 = $data['int1'] ?? $this->int1;
        $this->int2 = $data['int2'] ?? $this->int2;
    }
}
