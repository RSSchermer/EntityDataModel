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

use Rolab\EntityDataModel\NamedModelElement;
use Rolab\EntityDataModel\Type\ResourceType;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;

/**
 * Represents a structural data type. A structural type is not atomic,
 * it is build from other types. Maps to a class definition.
 * 
 * @author Roland Schermer <roland0507@gmail.com>
 */
abstract class StructuralType extends NamedModelElement implements ResourceType
{
    /**
     * @var ReflectionClass
     */
    private $reflection;
    
    /**
     * @var EntityDataModel
     */
    private $entityDataModel;
    
    /**
     * Creates a new structural type.
     * 
     * @param string          $name       The name of the structural type (may only
     *                                    consist of alphanumeric characters and the
     *                                    underscore).
     * @param ReflectionClass $reflection Reflection of the class this structural type
     *                                    maps to.
     * 
     * @throws InvalidArgumentException Thrown if the name contains illegal characters.
     */
    public function __construct($name, \ReflectionClass $reflection)
    {
        parent::__construct($name);
        
        $this->reflection = $reflection;
    }
    
    /**
     * Returns the reflection of the class this structural type maps to.
     * 
     * @return ReflectionClass A reflection of the class this structural type maps to.
     */
    public function getReflection()
    {
        return $this->reflection;
    }
    
    /**
     * Returns the name of the class this structural type maps to.
     * 
     * @return string The name of the class this structural type maps to.
     */
    public function getClassName()
    {
        return $this->reflection->getName();
    }
}
