<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Type;

use Rolab\EntityDataModel\Exception\InvalidArgumentException;

/**
 * Describes a complex property on a structured type.
 *
 * A complex property's value type is a complex type.
 *
 * @author Roland Schermer <roland0507@gmail.com>
 */
class ComplexPropertyDescription extends StructuralPropertyDescription
{
    /**
     * Creates a new complex property description.
     * 
     * @param string              $name              The name of the complex property description. (may
     *                                               only consist of alphanumeric characters and the
     *                                               underscore).
     * @param \ReflectionProperty $reflection        A reflection object for the property being described.
     * @param ComplexType         $propertyType      The type of the property value.
     * @param boolean             $isCollection      Whether or not the property value is a collection.
     * 
     * @throws InvalidArgumentException Thrown if the name contains illegal characters.
     *                                  Thrown if the property value type is an entity type.
     */
    public function __construct(
        string $name,
        \ReflectionProperty $reflection,
        ComplexType $propertyType,
        bool $isCollection = false
    ) {
        parent::__construct($name, $reflection, $propertyType, $isCollection);

        if ($propertyType instanceof EntityType) {
            throw new InvalidArgumentException(sprintf(
                'Tried to set entity type "%s" as the property value type of property "%s". The property value ' .
                'type of a complex property cannot be an entity type.',
                $propertyType->getFullName(),
                $name
            ));
        }
    }
}
