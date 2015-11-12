<?php

namespace Rolab\EntityDataModel\Annotations;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class PrimitiveProperty extends ResourceProperty
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
}
