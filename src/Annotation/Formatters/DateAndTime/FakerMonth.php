<?php

namespace RandomFakerBundle\Annotation\Formatters\DateAndTime;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerMonth
{
    public $max = 'now';

    public function __construct(array $data)
    {
        $this->max = $data['max'] ?? $this->max;
    }
}
