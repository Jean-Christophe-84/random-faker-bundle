<?php

namespace RandomFakerBundle\Attribute\Formatters\Payment;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerCreditCardNumber
{
    public function __construct(
        public ?string $type = null, public bool $formatted = false, public string $separator = '-'
    ) {
    }
}
