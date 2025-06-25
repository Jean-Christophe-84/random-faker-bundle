<?php

namespace RandomFakerBundle\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerIgnore
{
    public function __construct(public ?bool $ignore = null)
    {
    }
}
