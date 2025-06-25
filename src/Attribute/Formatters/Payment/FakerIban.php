<?php

namespace RandomFakerBundle\Attribute\Formatters\Payment;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerIban
{
    public function __construct(
        public ?string $countryCode = null, public string $prefix = '', public ?int $length = null
    ) {
    }
}
