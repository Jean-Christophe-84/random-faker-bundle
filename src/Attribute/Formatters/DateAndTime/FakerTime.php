<?php

namespace RandomFakerBundle\Attribute\Formatters\DateAndTime;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerTime
{
    public function __construct(
        public string $format = 'H:i:s', public \DateTime|int|string $max = 'now'
    ) {
    }
}
