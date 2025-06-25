<?php

namespace RandomFakerBundle\Annotation\Formatters\Image;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerImage
{
    public $dir       = null;

    public $width     = 640;

    public $height    = 480;

    public $category  = null;

    public $fullPath  = true;

    public $randomize = true;

    public $word      = null;

    public $gray      = false;

    public $format    = 'png';

    public function __construct(array $data)
    {
        $this->dir       = $data['dir'] ?? $this->dir;
        $this->width     = $data['width'] ?? $this->width;
        $this->height    = $data['height'] ?? $this->height;
        $this->category  = $data['category'] ?? $this->category;
        $this->fullPath  = $data['fullPath'] ?? $this->fullPath;
        $this->randomize = $data['randomize'] ?? $this->randomize;
        $this->word      = $data['word'] ?? $this->word;
        $this->gray      = $data['gray'] ?? $this->gray;
        $this->format    = $data['format'] ?? $this->format;
    }
}
