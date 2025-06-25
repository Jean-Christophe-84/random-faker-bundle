<?php

namespace RandomFakerBundle\Attribute\Locales\fr_FR\Payment;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerBankAccountNumber
{
    public function __construct(public string $prefix = '', public string $countryCode = 'FR', public ?int $length = null)
    {
    }
}
