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

use Rolab\EntityDataModel\Type\ResourcePropertyDescription;
use Rolab\EntityDataModel\Type\ResourceType;

/**
 * Describes a structural property of a complex type.
 * 
 * @author Roland Schermer <roland0507@gmail.com>
 */
abstract class StructuralPropertyDescription extends ResourcePropertyDescription
{
    /**
     * @var ResourceType
     */
    private $propertyValueType;
    
    /**
     * @var boolean
     */
    private $isCollection;
    
    /**
     * @var boolean
     */
    private $nullable;
    
    /**
     * Creates a new structural property description.
     * 
     * @param string             $name              The name of the structural property description. (may
     *                                              only consist of alphanumeric characters and the
     *                                              underscore).
     * @param ReflectionProperty $reflection        A reflection object for the property being described.
     * @param ResourceType       $propertyValueType The type of the property value.
     * @param boolean            $isCollection      Whether or not the property value is a collection.
     * 
     * @throws InvalidArgumentException Thrown if the name contains illegal characters.
     */
    public function __construct($name, \ReflectionProperty $reflection, ResourceType $propertyValueType,
        $isCollection = false
    ){
        parent::__construct($name, $reflection);

        $this->propertyValueType = $propertyValueType;
        $this->isCollection = $isCollection;
    }

    public function getPropertyValueType()
    {
        return $this->propertyValueType;
    }

    public function isCollection()
    {
        return (bool) $this->isCollection;
    }

    public function setNullable($nullable)
    {
        if (!is_bool($nullable)) {
            throw new InvalidArgumentException('Only boolean values are allowed.');
        }

        $this->nullable = $nullable;
    }

    public function isNullable()
    {
        return isset($this->nullable) ? $this->nullable : true;
    }
}
