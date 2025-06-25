<?php

namespace RandomFakerBundle\Attribute\Locales\fr_FR\Person;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerNir
{
    public function __construct(public ?string $gender = null, public bool $formatted = false)
    {
    }
}
