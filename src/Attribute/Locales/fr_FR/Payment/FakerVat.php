<?php

namespace RandomFakerBundle\Attribute\Locales\fr_FR\Payment;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerVat
{
    public function __construct(public bool $spacedNationalPrefix = true)
    {
    }
}
