<?php

namespace RandomFakerBundle\Annotation\Formatters\DateAndTime;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerDateTime
{
    public \DateTime|int|string $max      = 'now';

    public ?string              $timezone = null;

    public function __construct(array $data)
    {
        $this->max      = $data['max'] ?? $this->max;
        $this->timezone = $data['timezone'] ?? $this->timezone;
    }
}
