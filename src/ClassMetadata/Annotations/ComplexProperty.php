<?php

namespace RSSchermer\EntityModel\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class ComplexProperty extends Annotation
{
    /** @var string @Required */
    public $class;

    /** @var string */
    public $name;

    /** @var boolean */
    public $nullable = true;

    /** @var boolean */
    public $isCollection = false;
}
