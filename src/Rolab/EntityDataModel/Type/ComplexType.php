<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\EntityDataModel\Type;

use Rolab\EntityDataModel\Type\StructuralType;
use Rolab\EntityDataModel\Type\ResourcePropertyDescription;
use Rolab\EntityDataModel\Type\StructuralPropertyDescription;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;
use Rolab\EntityDataModel\Exception\RuntimeException;

/**
 * Represents a complex data type with one or more properties.
 * 
 * @author Roland Schermer <roland0507@gmail.com>
 */
class ComplexType extends StructuralType
{
    /**
     * @var array
     */
    private $structuralPropertyDescriptions = array();
    
    /**
     * Creates a new complex type.
     * 
     * @param string                        $name                 The name of the complex type (may only
     *                                                            contain alphanumeric characters and the
     *                                                            underscore).
     * @param ReflectionClass               $reflection           Reflection of the class this structural
     *                                                            type maps to.
     * @param ResourcePropertyDescription[] $propertyDescriptions Descriptions for each of the properties.
     * 
     * @throws InvalidArgumentException Thrown if the name contains illegal characters.
     *                                  Thrown if the property description list is empty.
     */
    public function __construct($name, \ReflectionClass $reflection, array $propertyDescriptions)
    {
        parent::__construct($name, $reflection);

        if (count($propertyDescriptions) === 0) {
            throw new InvalidArgumentException('May not pass an empty array of property descriptions. A complex ' .
                'type must always have atleast one property.');
        }

        foreach ($propertyDescriptions as $propertyDescription) {
            $this->addPropertyDescription($propertyDescription);
        }
    }
    
    /**
     * Adds a property description to the complex type.
     * 
     * Adds a property description to the complex type. No two properties on the same
     * complex type may have the same name.
     * 
     * @param ResourcePropertyDescription $propertyDescription The property description to
     *                                                         be added to the complex type.
     * 
     * @throws InvalidArgumentException Thrown if the complex type already has a property with
     *                                  the same name.
     */
    public function addPropertyDescription(ResourcePropertyDescription $propertyDescription)
    {
        $propertyDescriptions = $this->getPropertyDescriptions();

        if (isset($propertyDescriptions[$propertyDescription->getName()])) {
            throw new InvalidArgumentException(sprintf('Type "%s" already has a property named "%s"',
                $this->getName(), $propertyDescription->getName()));
        }

        $this->addStructuralPropertyDescription($propertyDescription);
    }
    
    /**
     * Removes a property description from the complex type.
     * 
     * Removes a property description from the complex type if a property with the name
     * specified exists. Complex types must always remain with at least one property.
     * 
     * @param string $propertyDescriptionName The name of the property to be removed.
     * 
     * @throws InvalidArgumentException Thrown if the property removed was the last remaining
     *                                  property of the complex type.
     */
    public function removePropertyDescription($propertyDescriptionName)
    {
        unset($this->structuralPropertyDescriptions[$propertyDescriptionName]);

        if (count($this->getPropertyDescriptions()) === 0) {
            throw new RuntimeException('A complex type must keep atleast one property.');
        }
    }
    
    /**
     * Returns the property descriptions for this complex type.
     * 
     * @return ResourcePropertyDescription[] The property descriptions for this complex type.
     */
    public function getPropertyDescriptions()
    {
        return $this->getStructuralPropertyDescriptions();
    }
    
    /**
     * Returns the structural property descriptions for this complex type.
     * 
     * Returns only the structural property descriptions for this complex type, not
     * the navigation property descriptions.
     * 
     * @return StructuralPropertyDescription[] The structural property descriptions for this
     *                                         complex type.
     */
    public function getStructuralPropertyDescriptions()
    {
        return $this->structuralPropertyDescriptions;
    }
    
    /**
     * Searches for a property description on this complex type bases on its name.
     * 
     * @return null|ResourcePropertyDescription[] Returns the resource property with the name searched
     *                                            for or null if no such property exists.
     */
    public function getPropertyDescriptionByName($propertyDescriptionName)
    {
        $propertyDescriptions = $this->getPropertyDescriptions();

        return isset($propertyDescriptions[$propertyDescriptionName]) ?
            $propertyDescriptions[$propertyDescriptionName] : null;
    }

    protected function addStructuralPropertyDescription(StructuralPropertyDescription $propertyDescription)
    {
        $this->structuralPropertyDescriptions[$propertyDescription->getName()] = $propertyDescription;
    }
}
