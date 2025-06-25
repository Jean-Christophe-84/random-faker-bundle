<?php

namespace RandomFakerBundle\Annotation\AvailableFormatters\Address;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerLatitude
{
    public float $min = -90;

    public float $max = 90;

    public function __construct(array $data)
    {
        $this->min = $data['min'] ?? $this->min;
        $this->max = $data['max'] ?? $this->max;
    }
}
