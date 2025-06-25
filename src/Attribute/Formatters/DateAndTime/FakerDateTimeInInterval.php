<?php

namespace RandomFakerBundle\Attribute\Formatters\DateAndTime;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerDateTimeInInterval
{
    public function __construct(
        public \DateTime|string $date = '-30 years', public string $interval = '+5 days', public ?string $timezone = null
    ) {
    }
}
