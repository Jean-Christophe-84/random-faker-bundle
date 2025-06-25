<?php

namespace RandomFakerBundle\Annotation\Locales\fr_FR\Company;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerSiren
{
    public bool $formatted = true;

    public function __construct(array $data)
    {
        $this->formatted = $data['formatted'] ?? $this->formatted;
    }
}
