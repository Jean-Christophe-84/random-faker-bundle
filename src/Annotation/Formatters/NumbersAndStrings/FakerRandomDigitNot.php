<?php

namespace RandomFakerBundle\Annotation\Formatters\NumbersAndStrings;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerRandomDigitNot
{
    public $except;

    public function __construct(array $data)
    {
        $this->except = $data['except'];
    }
}
