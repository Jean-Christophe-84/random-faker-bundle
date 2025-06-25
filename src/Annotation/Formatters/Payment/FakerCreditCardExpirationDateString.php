<?php

namespace RandomFakerBundle\Annotation\Formatters\Payment;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerCreditCardExpirationDateString
{
    public bool    $valid                = true;

    public ?string $expirationDateFormat = null;

    public function __construct(array $data)
    {
        $this->valid                = $data['valid'] ?? $this->valid;
        $this->expirationDateFormat = $data['expirationDateFormat'] ?? $this->expirationDateFormat;
    }
}
