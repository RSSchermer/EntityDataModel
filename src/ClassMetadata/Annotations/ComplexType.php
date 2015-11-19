<?php

namespace RSSchermer\EntityModel\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class ComplexType extends Annotation
{
    /** @var string */
    public $name;
}
