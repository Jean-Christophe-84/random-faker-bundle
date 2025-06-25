<?php

namespace RandomFakerBundle\Annotation\Formatters\Internet;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerSlug
{
    public int  $nbWords         = 6;

    public bool $variableNbWords = true;

    public function __construct(array $data)
    {
        $this->nbWords         = $data['nbWords'] ?? $this->nbWords;
        $this->variableNbWords = $data['variableNbWords'] ?? $this->variableNbWords;
    }
}
