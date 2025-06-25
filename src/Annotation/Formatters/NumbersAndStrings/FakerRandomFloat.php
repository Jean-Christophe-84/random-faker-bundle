<?php

namespace RandomFakerBundle\Annotation\Formatters\NumbersAndStrings;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerRandomFloat
{
    public $nbMaxDecimals = null;

    public $min           = 0;

    public $max           = null;

    public function __construct(array $data)
    {
        $this->nbMaxDecimals = $data['nbMaxDecimals'] ?? $this->nbMaxDecimals;
        $this->min = $data['min'] ?? $this->min;
        $this->max = $data['max'] ?? $this->max;
    }
}
