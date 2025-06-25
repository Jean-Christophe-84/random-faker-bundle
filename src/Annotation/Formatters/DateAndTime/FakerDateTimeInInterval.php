<?php

namespace RandomFakerBundle\Annotation\Formatters\DateAndTime;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerDateTimeInInterval
{
    public $date     = '-30 years';

    public $interval = '+5 days';

    public $timezone = null;

    public function __construct(array $data)
    {
        $this->date     = $data['date'] ?? $this->date;
        $this->interval = $data['max'] ?? $this->interval;
        $this->timezone = $data['timezone'] ?? $this->timezone;
    }
}
