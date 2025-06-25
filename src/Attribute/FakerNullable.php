<?php

namespace RandomFakerBundle\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerNullable
{
    public function __construct(public ?bool $nullable = null)
    {
    }
}
