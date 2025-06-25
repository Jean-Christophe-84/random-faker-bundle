<?php

namespace RandomFakerBundle\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerOrder
{
    public function __construct(public int $order)
    {
    }
}
