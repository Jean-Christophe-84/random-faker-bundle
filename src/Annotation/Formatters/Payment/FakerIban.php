<?php

namespace RandomFakerBundle\Annotation\Payment;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerIban
{
    public $countryCode = null;

    public $prefix      = '';

    public $length      = null;

    public function __construct(array $data)
    {
        $this->countryCode = $data['countryCode'] ?? $this->countryCode;
        $this->prefix      = $data['prefix'] ?? $this->prefix;
        $this->length      = $data['length'] ?? $this->length;
    }
}
