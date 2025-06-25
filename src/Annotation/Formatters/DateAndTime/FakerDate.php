<?php

namespace RandomFakerBundle\Annotation\Formatters\DateAndTime;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerDate
{
    public $format = 'Y-m-d';

    public $max    = 'now';

    public function __construct(array $data)
    {
        $this->format = $data['$format'] ?? $this->format;
        $this->max    = $data['max'] ?? $this->max;
    }
}
