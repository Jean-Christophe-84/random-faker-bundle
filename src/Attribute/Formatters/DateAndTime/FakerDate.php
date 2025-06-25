<?php

namespace RandomFakerBundle\Attribute\Formatters\DateAndTime;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerDate
{
    public function __construct(
        public string $format = 'Y-m-d', public \DateTime|int|string $max = 'now'
    ) {
    }
}
