<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Type;

use PhpCollection\Map;
use PhpCollection\MapInterface;
use PhpOption\Option;

use Rolab\EntityDataModel\Exception\InvalidArgumentException;

/**
 * Represents a complex data type with one or more properties.
 *
 * Complex types are keyless structured types. This means that instances of complex types
 * can not be referenced be referenced independently of an "owner" entity type. Complex
 * types allow models to group common properties into common structures, for example an
 * "Address" type with "City", "Street" and "ZipCode" properties.
 * 
 * @author Roland Schermer <roland0507@gmail.com>
 */
class ComplexType extends StructuredType
{
    /**
     * @var MapInterface
     */
    private $structuralPropertyDescriptions;
    
    /**
     * Creates a new complex type.
     * 
     * @param string                          $name                           The name of the complex type (may only
     *                                                                        contain alphanumeric characters and the
     *                                                                        underscore).
     * @param \ReflectionClass                $reflection                     Reflection of the class this structural
     *                                                                        type maps to.
     * @param StructuralPropertyDescription[] $structuralPropertyDescriptions Descriptions for each of the properties.
     * 
     * @throws InvalidArgumentException Thrown if the name contains illegal characters.
     *                                  Thrown if the property description list is empty.
     */
    public function __construct(
        string $name,
        \ReflectionClass $reflection,
        array $structuralPropertyDescriptions = array()
    ) {
        parent::__construct($name, $reflection);

        $this->structuralPropertyDescriptions = new Map();

        foreach ($structuralPropertyDescriptions as $propertyDescription) {
            $this->addStructuralPropertyDescription($propertyDescription);
        }
    }
    
    /**
     * Adds a property description to the complex type.
     * 
     * Adds a property description to the complex type. No two properties on the same
     * complex type may have the same name.
     * 
     * @param StructuralPropertyDescription $propertyDescription The property description to
     *                                                           be added to the complex type.
     * 
     * @throws InvalidArgumentException Thrown if the complex type already has a property with
     *                                  the same name.
     */
    public function addStructuralPropertyDescription(StructuralPropertyDescription $propertyDescription)
    {
        if ($this->getPropertyDescriptions()->containsKey($propertyDescription->getName())) {
            throw new InvalidArgumentException(sprintf(
                'Tried to add structural property "%s" to structured type "%s", but this entity type already has a ' .
                'property with this name. The names of the properties defined on a structured type must be unique.',
                $this->getName(),
                $propertyDescription->getName()
            ));
        }

        $this->structuralPropertyDescriptions->set($propertyDescription->getName(), $propertyDescription);
        $propertyDescription->setStructuredType($this);
    }
    
    /**
     * Return a map of property descriptions for this complex type keyed
     * by property name.
     * 
     * @return MapInterface A map of property descriptions for this complex type keyed
     *                      by property name.
     */
    public function getPropertyDescriptions() : MapInterface
    {
        return $this->getStructuralPropertyDescriptions();
    }

    /**
     * Returns a map of structural property descriptions for this complex type keyed
     * by property name.
     *
     * Returns only the structural property descriptions for this complex type, not
     * the navigation property descriptions.
     *
     * @return MapInterface A map of structural property descriptions for this complex type keyed
     *                      by property name.
     */
    public function getStructuralPropertyDescriptions() : MapInterface
    {
        return $this->structuralPropertyDescriptions;
    }
    
    /**
     * Searches for a property description on this complex type bases on its name.
     *
     * @param string $propertyDescriptionName The name of the property description to search for.
     * 
     * @return Option Returns the resource property with the name searched for wrapped in Some,
     *                or None if no such property exists.
     */
    public function getPropertyDescriptionByName(string $propertyDescriptionName) : Option
    {
        return $this->getPropertyDescriptions()->get($propertyDescriptionName);
    }
}
