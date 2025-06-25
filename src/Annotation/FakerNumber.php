<?php

namespace RandomFakerBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerNumber
{
    public $number;

    public function __construct(array $data)
    {
        $this->number = $data['number'];
    }
}
