<?php

namespace RandomFakerBundle\Annotation\AvailableFormatters\Address;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerLongitude
{
    public float $min = -180;

    public float $max = 180;

    public function __construct(array $data)
    {
        $this->min = $data['min'] ?? $this->min;
        $this->max = $data['max'] ?? $this->max;
    }
}
