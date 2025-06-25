<?php

namespace RandomFakerBundle\Annotation\Formatters\DateAndTime;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerYear
{
    public \DateTime|int|string $max = 'now';

    public function __construct(array $data)
    {
        $this->max = $data['max'] ?? $this->max;
    }
}
