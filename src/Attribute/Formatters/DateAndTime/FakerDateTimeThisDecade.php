<?php

namespace RandomFakerBundle\Attribute\Formatters\DateAndTime;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerDateTimeThisDecade
{
    public function __construct(
        public \DateTime|int|string $max = 'now', public ?string $timezone = null
    ) {
    }
}
