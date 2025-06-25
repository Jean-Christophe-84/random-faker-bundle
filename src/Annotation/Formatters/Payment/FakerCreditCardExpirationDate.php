<?php

namespace RandomFakerBundle\Annotation\Payment;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerCreditCardExpirationDate
{
    public $valid = true;

    public function __construct(array $data)
    {
        $this->valid = $data['valid'] ?? $this->valid;
    }
}
