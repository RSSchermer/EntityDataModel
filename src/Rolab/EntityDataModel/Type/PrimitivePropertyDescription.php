<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Type;

use Rolab\EntityDataModel\Exception\InvalidArgumentException;

/**
 * Describes a primitive property of a complex type.
 * 
 * @author Roland Schermer <roland0507@gmail.com>
 */
class PrimitivePropertyDescription extends StructuralPropertyDescription
{
    /**
     * Creates a new primitive property description.
     * 
     * @param string              $name              The name of the primitive property description. (may
     *                                               only consist of alphanumeric characters and the
     *                                               underscore).
     * @param \ReflectionProperty $reflection        A reflection object for the property being described.
     * @param PrimitiveType       $propertyType      The type of the property value.
     * @param boolean             $isCollection      Whether or not the property value is a collection.
     * 
     * @throws InvalidArgumentException Thrown if the name contains illegal characters.
     */
    public function __construct(
        string $name,
        \ReflectionProperty $reflection,
        PrimitiveType $propertyType,
        bool $isCollection = false
    ) {
        parent::__construct($name, $reflection, $propertyType, $isCollection);
    }
}
