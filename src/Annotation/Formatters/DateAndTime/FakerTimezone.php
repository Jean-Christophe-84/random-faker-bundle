<?php

namespace RandomFakerBundle\Annotation\Formatters\DateAndTime;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerTimezone
{
    public $countryCode = null;

    public function __construct(array $data)
    {
        $this->countryCode = $data['countryCode'] ?? $this->countryCode;
    }
}
