<?php

namespace RandomFakerBundle\Attribute\Formatters\DateAndTime;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerDateTimeBetween
{
    public function __construct(public \DateTime|string $startDate = '-30 years', public \DateTime|string $endDate = 'now', public ?string $timezone = null)
    {
    }
}
