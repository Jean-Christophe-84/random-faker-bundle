<?php

namespace RandomFakerBundle\Annotation\AvailableFormatters\Person;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerName
{
    public ?string $gender = null;

    public function __construct(array $data)
    {
        $this->gender = $data['gender'] ?? $this->gender;
    }
}
