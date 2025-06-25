<?php

namespace RandomFakerBundle\Attribute\Formatters\File;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerFile
{
    public function __construct(public string $sourceDirectory = '/tmp', public string $targetDirectory = '/tmp', public bool $fullPath = true)
    {
    }
}
