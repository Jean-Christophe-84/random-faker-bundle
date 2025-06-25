<?php

namespace RandomFakerBundle\Annotation\Formatters\Miscellaneous;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerBoolean
{
    public $chanceOfGettingTrue = 50;

    public function __construct(array $data)
    {
        $this->chanceOfGettingTrue = $data['chanceOfGettingTrue'] ?? $this->chanceOfGettingTrue;
    }
}
