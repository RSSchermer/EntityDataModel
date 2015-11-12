<?php

namespace Rolab\EntityDataModel\Annotations;

use Doctrine\Common\Annotations\Annotation;

abstract class ResourceProperty extends Annotation
{
    /** @var string */
    public $name;
    
    /** @var boolean */
    public $nullable = true;
    
    /** @var boolean */
    public $isCollection = false;
}
