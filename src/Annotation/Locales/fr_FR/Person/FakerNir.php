<?php

namespace RandomFakerBundle\Annotation\Locales\fr_FR\Person;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerNir
{
    public ?string $gender = null;

    public bool $formatted = false;

    public function __construct(array $data)
    {
        $this->gender    = $data['gender'] ?? $this->gender;
        $this->formatted = $data['formatted'] ?? $this->formatted;
    }
}
