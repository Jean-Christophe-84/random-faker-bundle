<?php

namespace RandomFakerBundle\Annotation\Formatters\DateAndTime;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerDateTimeInInterval
{
    public \DateTime|string $date     = '-30 years';

    public string           $interval = '+5 days';

    public ?string          $timezone = null;

    public function __construct(array $data)
    {
        $this->date     = $data['date'] ?? $this->date;
        $this->interval = $data['max'] ?? $this->interval;
        $this->timezone = $data['timezone'] ?? $this->timezone;
    }
}
