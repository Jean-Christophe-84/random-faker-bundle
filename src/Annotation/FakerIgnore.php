<?php

namespace RandomFakerBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerIgnore
{
    public ?bool $ignore = null;

    public function __construct(array $data)
    {
        $this->ignore = $data['ignore'] ?? $this->ignore;
    }
}
