<?php

namespace RandomFakerBundle\Attribute\AvailableFormatters\Person;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerTitle
{
    public function __construct(public ?string $gender = null)
    {
    }
}
