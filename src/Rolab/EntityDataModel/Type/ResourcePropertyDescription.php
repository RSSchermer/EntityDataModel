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

abstract class ResourcePropertyDescription
{
    private $name;

    private $reflection;

    private $propertyPossessingStructuralType;

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

    public function setPropertyPossessingStructuralType(StructuralType $propertyPossessingStructuralType)
    {
        $this->propertyPossessingStructuralType = $propertyPossessingStructuralType;
    }

    public function getPropertyPossessingStructuralType()
    {
        return $this->propertyPossessingStructuralType;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getReflection()
    {
        return $this->reflection;
    }
}
