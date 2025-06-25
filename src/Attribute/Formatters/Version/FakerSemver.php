<?php

namespace RandomFakerBundle\Attribute\Formatters\Version;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerSemver
{
    public function __construct(public bool $preRelease = false, public bool $build = false)
    {
    }
}
