<?php

namespace RandomFakerBundle\Annotation\Formatters\NumbersAndStrings;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerRegexify
{
    public string $regex = '';

    public function __construct(array $data)
    {
        $this->regex = $data['regex'] ?? $this->regex;
    }
}
