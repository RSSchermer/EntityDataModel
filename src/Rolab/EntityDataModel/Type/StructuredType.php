<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Type;

use Rolab\EntityDataModel\NamedModelElement;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;

/**
 * Represents a structural data type. A structured type is not atomic,
 * it is build from other types. Maps to a class definition.
 * 
 * @author Roland Schermer <roland0507@gmail.com>
 */
abstract class StructuredType extends NamedModelElement implements ResourceType
{
    /**
     * @var \ReflectionClass
     */
    private $reflection;

    /**
     * Creates a new structured type.
     * 
     * @param string           $name       The name of the structured type (may only
     *                                     consist of alphanumeric characters and the
     *                                     underscore).
     * @param \ReflectionClass $reflection Reflection of the class this structured type
     *                                     maps to.
     * 
     * @throws InvalidArgumentException Thrown if the name contains illegal characters.
     */
    public function __construct(string $name, \ReflectionClass $reflection)
    {
        parent::__construct($name);
        
        $this->reflection = $reflection;
    }
    
    /**
     * Returns the reflection of the class this structured type maps to.
     * 
     * @return \ReflectionClass A reflection of the class this structured type maps to.
     */
    public function getReflection() : \ReflectionClass
    {
        return $this->reflection;
    }
    
    /**
     * Returns the name of the class this structured type maps to.
     * 
     * @return string The name of the class this structured type maps to.
     */
    public function getClassName() : string
    {
        return $this->reflection->getName();
    }
}
