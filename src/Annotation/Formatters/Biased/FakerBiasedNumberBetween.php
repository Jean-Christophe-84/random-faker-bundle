<?php

namespace RandomFakerBundle\Annotation\Formatters\Biased;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerBiasedNumberBetween
{
    public $min      = 0;

    public $max      = 100;

    public $function = 'sqrt';

    public function __construct(array $data)
    {
        $this->min      = $data['min'] ?? $this->min;
        $this->max      = $data['max'] ?? $this->max;
        $this->function = $data['function'] ?? $this->function;
    }
}
