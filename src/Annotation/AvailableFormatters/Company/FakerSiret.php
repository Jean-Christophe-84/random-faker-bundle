<?php

namespace RandomFakerBundle\Annotation\AvailableFormatters\Company;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerSiret
{
    public bool $formatted = true;

    public function __construct(array $data)
    {
        $this->formatted = $data['formatted'] ?? $this->formatted;
    }
}
