<?php

namespace RandomFakerBundle\Annotation\Formatters\Image;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerImageUrl
{
    public int     $width     = 640;

    public int     $height    = 480;

    public ?string $category  = null;

    public bool    $randomize = true;

    public ?string $word      = null;

    public bool    $gray      = false;

    public string  $format    = 'png';

    public function __construct(array $data)
    {
        $this->width     = $data['width'] ?? $this->width;
        $this->height    = $data['height'] ?? $this->height;
        $this->category  = $data['category'] ?? $this->category;
        $this->randomize = $data['randomize'] ?? $this->randomize;
        $this->word      = $data['word'] ?? $this->word;
        $this->gray      = $data['gray'] ?? $this->gray;
        $this->format    = $data['format'] ?? $this->format;
    }
}
