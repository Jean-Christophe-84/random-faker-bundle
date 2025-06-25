<?php

namespace RandomFakerBundle\Annotation\Locales\fr_FR\Payment;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerVat
{
    public $spacedNationalPrefix = true;

    public function __construct(array $data)
    {
        $this->spacedNationalPrefix = $data['spacedNationalPrefix'] ?? $this->spacedNationalPrefix;
    }
}
