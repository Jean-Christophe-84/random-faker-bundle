<?php

namespace RandomFakerBundle\Attribute\Formatters\Image;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class FakerImageUrl
{
    public function __construct(
        public int  $width = 640, public int $height = 480, public ?string $category = null, public bool $randomize = true, public ?string $word = null,
        public bool $gray = false, public string $format = 'png'
    ) {
    }
}
