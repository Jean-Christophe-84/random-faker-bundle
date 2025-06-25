<?php

namespace RandomFakerBundle\Annotation\AvailableFormatters\Person;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerTitle
{
    public $gender = null;

    public function __construct(array $data)
    {
        $this->gender = $data['gender'] ?? $this->gender;
    }
}
