<?php

namespace RandomFakerBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerNullable
{
    public $nullable = null;

    public function __construct(array $data)
    {
        $this->nullable = $data['nullable'] ?? $this->nullable;
    }
}
