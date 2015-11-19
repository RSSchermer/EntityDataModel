<?php

namespace RSSchermer\EntityModel\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class PrimitiveProperty extends Annotation
{
    /**
     * @Enum({
     *    'Binary',
     *    'Boolean',
     *    'Byte',
     *    'Date',
     *    'DateTimeOffset',
     *    'Decimal',
     *    'Double',
     *    'Duration',
     *    'Guid',
     *    'Int16',
     *    'Int32',
     *    'Int64',
     *    'SByte',
     *    'Single',
     *    'Stream',
     *    'String',
     *    'TimeOfDay'
     * })
     * @Required
     */
    public $type;

    /** @var string */
    public $name;

    /** @var boolean */
    public $nullable = true;

    /** @var boolean */
    public $isCollection = false;
}
