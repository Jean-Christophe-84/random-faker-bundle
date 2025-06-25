<?php

namespace RandomFakerBundle\Annotation\Formatters\DateAndTime;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerDateTimeBetween
{
    public $startDate = '-30 years';

    public $endDate   = 'now';

    public $timezone  = null;

    public function __construct(array $data)
    {
        $this->startDate = $data['startDate'] ?? $this->startDate;
        $this->endDate   = $data['endDate'] ?? $this->endDate;
        $this->timezone  = $data['timezone'] ?? $this->timezone;
    }
}
