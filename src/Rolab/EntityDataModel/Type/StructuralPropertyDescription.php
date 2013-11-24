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

abstract class StructuralPropertyDescription extends ResourcePropertyDescription
{
    private $propertyValueType;

    private $isCollection;

    private $nullable;

    private $maxLength;

    private $fixedLength;

    private $precision;

    private $scale;

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
