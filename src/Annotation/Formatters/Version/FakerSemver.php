<?php

namespace RandomFakerBundle\Annotation\Version;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerSemver
{
    public $preRelease = false;

    public $build      = false;

    public function __construct(array $data)
    {
        $this->preRelease = $data['preRelease'] ?? $this->preRelease;
        $this->build      = $data['build'] ?? $this->build;
    }
}
