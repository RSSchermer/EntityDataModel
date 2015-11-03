<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Type;

use Rolab\EntityDataModel\Exception\InvalidArgumentException;

/**
 * Describes a key property of an entity type.
 * 
 * @author Roland Schermer <roland0507@gmail.com>
 */
class KeyPropertyDescription extends PrimitivePropertyDescription
{
    /**
     * Creates a new key property description.
     * 
     * @param string              $name              The name of the key property description. (may
     *                                               only consist of alphanumeric characters and the
     *                                               underscore).
     * @param \ReflectionProperty $reflection        A reflection object for the property being described.
     * @param PrimitiveType       $propertyType      The type of the property value.
     * 
     * @throws InvalidArgumentException Thrown if the name contains illegal characters.
     */
    public function __construct(string $name, \ReflectionProperty $reflection, PrimitiveType $propertyType)
    {
        parent::__construct($name, $reflection, $propertyType, false);
    }
}
