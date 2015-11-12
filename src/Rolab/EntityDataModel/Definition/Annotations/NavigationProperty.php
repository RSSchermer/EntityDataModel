<?php

namespace Rolab\EntityDataModel\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class NavigationProperty extends ResourceProperty
{
    /**
     * @var string
     * @Required
     */
    public $target;

    /**
     * @var string
     */
    public $partner;

    /** @Enum({'none', 'cascade', 'set_null', 'set_default'}) */
    public $onDeleteAction = 'none';
}
