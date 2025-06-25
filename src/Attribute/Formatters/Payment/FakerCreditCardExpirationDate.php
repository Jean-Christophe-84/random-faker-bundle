<?php

namespace RandomFakerBundle\Attribute\Formatters\Payment;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerCreditCardExpirationDate
{
    public function __construct(public bool $valid = true)
    {
    }
}
