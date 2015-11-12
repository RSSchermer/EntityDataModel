<?php

namespace Rolab\EntityDataModel\Annotations;

use Doctrine\Common\Annotations\Annotation;

abstract class StructuralType extends Annotation
{
    /** @var string */
    public $name;
}
