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

use Rolab\EntityDataModel\Exception\InvalidArgumentException;
use Rolab\EntityDataModel\Type\ComplexType;

/**
 * Describes a property of a complex type.
 * 
 * @author Roland Schermer <roland0507@gmail.com>
 */
abstract class ResourcePropertyDescription
{
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var ReflectionProperty
     */
    private $reflection;
    
    /**
     * @var ComplexType
     */
    private $complexType;
    
    /**
     * Creates a new resource property description.
     * 
     * @param string             $name       The name of the resource property description (may
     *                                       only consist of alphanumeric characters and the
     *                                       underscore).
     * @param ReflectionProperty $reflection A reflection object for the property being described.
     * 
     * @throws InvalidArgumentException Thrown if the name contains illegal characters.
     */
    public function __construct($name, \ReflectionProperty $reflection)
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) {
            throw new InvalidArgumentException(sprintf(
                '"%s" is an illegal name for a property description. ' .
                'The name for a property descriptions may only contain alphanumeric characters and underscores.',
                $name
            ));
        }

        $this->name = $name;
        $this->reflection = $reflection;
    }
    
    /**
     * Sets the complex type this resource property description belongs to.
     * 
     * @param ComplexType $complexType The complex type this resource property description
     *                                 belongs to.
     */
    public function setComplexType(ComplexType $complexType)
    {
        $this->complexType = $complexType;
    }
    
    /**
     * Returns the complex type this resource property description belongs to.
     * 
     * @return null|ComplexType The complex type this resource property description belongs to.
     */
    public function getComplexType()
    {
        return $this->complexType;
    }
    
    /**
     * Returns the name of the resource property description.
     * 
     * @return string The name of the resource property description.
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Returns the reflection for the property that is described by the resource
     * property description.
     * 
     * @return ReflectionProperty The reflection for the property that is described by
     *                            the resource property description.
     */
    public function getReflection()
    {
        return $this->reflection;
    }
}
