<?php

namespace RandomFakerBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerOrder
{
    public $order;

    public function __construct(array $data)
    {
        $this->order = $data['order'];
    }
}
