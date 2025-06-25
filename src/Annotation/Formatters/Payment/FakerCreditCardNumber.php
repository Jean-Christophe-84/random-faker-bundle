<?php

namespace RandomFakerBundle\Annotation\Payment;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerCreditCardNumber
{
    public ?string $type      = null;

    public bool    $formatted = false;

    public string  $separator = '-';

    public function __construct(array $data)
    {
        $this->type      = $data['type'] ?? $this->type;
        $this->formatted = $data['formatted'] ?? $this->formatted;
        $this->separator = $data['separator'] ?? $this->separator;
    }
}
