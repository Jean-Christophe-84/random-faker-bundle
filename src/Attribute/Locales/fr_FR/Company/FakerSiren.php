<?php

namespace RandomFakerBundle\Attribute\Locales\fr_FR\Company;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerSiren
{
    public function __construct(public bool $formatted = true)
    {
    }
}
