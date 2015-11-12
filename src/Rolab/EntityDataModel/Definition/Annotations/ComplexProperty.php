<?php

namespace Rolab\EntityDataModel\Annotations;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class ComplexProperty extends ResourceProperty
{
    /**
     * @var string
     * @Required
     */
    public $class;
}
