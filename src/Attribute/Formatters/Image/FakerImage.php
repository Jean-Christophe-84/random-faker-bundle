<?php

namespace RandomFakerBundle\Attribute\Formatters\Image;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerImage
{
    public function __construct(
        public ?string $dir = null, public int $width = 640, public int $height = 480, public ?string $category = null, public bool $fullPath = true,
        public bool    $randomize = true, public ?string $word = null, public bool $gray = false, public string $format = 'png'
    ) {
    }
}
