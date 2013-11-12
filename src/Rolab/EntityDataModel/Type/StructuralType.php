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

use Rolab\EntityDataModel\Type\ResourceType;
use Rolab\EntityDataModel\Type\PropertyDescription\ResourcePropertyDescription;
use Rolab\EntityDataModel\Type\PropertyDescription\RegularPropertyDescription;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;

abstract class StructuralType extends ResourceType
{
    private $name;

    private $namespace;
    
    private $reflection;

    private $regularPropertyDescriptions = array();

    private $baseType;

    public function __construct($name, $namespace, \ReflectionClass $reflection, array $propertyDescriptions = array(), 
        StructuralType $baseType = null
    ){
        $this->name = $name;
        $this->namespace = $namespace;
        $this->reflection = $reflection;
        $this->setPropertyDescriptions($propertyDescriptions);
        $this->baseType = $baseType;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }
    
    public function getReflection()
    {
        return $this->reflection;
    }

    public function getFullName()
    {
        return isset($this->namespace) ?  $this->namespace .'.'. $this->name : $this->name;
    }

    public function getPropertyDescriptions()
    {
        return $this->getRegularPropertyDescriptions();
    }

    public function getRegularPropertyDescriptions()
    {
        return isset($this->baseType) ? 
            array_merge($this->baseType->getRegularPropertyDescriptions(), $this->regularPropertyDescriptions) :
            $this->regularPropertyDescriptions;
    }

    public function setPropertyDescriptions(array $propertyDescriptions)
    {
        foreach ($propertyDescriptions as $propertyDescription) {
            $this->addPropertyDescription($propertyDescription);
        }
    }

    public function addPropertyDescription(ResourcePropertyDescription $propertyDescription)
    {
        $this->addRegularPropertyDescription($propertyDescription);
    }

    public function addRegularPropertyDescription(RegularPropertyDescription $propertyDescription)
    {
        $propertyDescriptions = $this->getPropertyDescriptions();

        if (isset($propertyDescriptions[$propertyDescription->getName()])) {
            throw new InvalidArgumentException(sprintf('Type "%s" already has a property named "%s"',
                $this->getFullName(), $propertyDescription->getName()));
        }

        $this->regularPropertyDescriptions[$propertyDescription->getName()] = $propertyDescription;
    }

    public function removePropertyDescription($propertyDescriptionName)
    {
        unset($this->regularPropertyDescriptions[$propertyDescriptionName]);
    }

    public function getPropertyDescriptionByName($propertyDescriptionName)
    {
        $propertyDescriptions = $this->getPropertyDescriptions();

        return $propertyDescriptions[$propertyDescriptionName];
    }
}
