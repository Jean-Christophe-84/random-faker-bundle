<?php

namespace RandomFakerBundle\Annotation\Formatters\File;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class FakerFile
{
    public $sourceDirectory = '/tmp';

    public $targetDirectory = '/tmp';

    public $fullPath        = true;

    public function __construct(array $data)
    {
        $this->sourceDirectory = $data['sourceDirectory'] ?? $this->sourceDirectory;
        $this->targetDirectory = $data['targetDirectory'] ?? $this->targetDirectory;
        $this->fullPath        = $data['fullPath'] ?? $this->fullPath;
    }
}
