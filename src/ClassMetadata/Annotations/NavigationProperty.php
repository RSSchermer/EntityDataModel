<?php

namespace RSSchermer\EntityModel\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class NavigationProperty extends Annotation
{
    /** @var string @Required */
    public $target;

    /** @var string */
    public $name;

    /** @var boolean */
    public $nullable = true;

    /** @var boolean */
    public $isCollection = false;

    /** @var string */
    public $partner;

    /** @Enum({'none', 'cascade', 'set_null', 'set_default'}) */
    public $onDeleteAction = 'none';
}
