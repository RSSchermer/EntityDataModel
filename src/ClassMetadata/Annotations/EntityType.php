<?php

namespace RSSchermer\EntityModel\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class EntityType extends Annotation
{
    /** @var string */
    public $name;
}
