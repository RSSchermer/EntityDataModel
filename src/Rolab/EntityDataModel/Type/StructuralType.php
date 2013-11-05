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

use Rolab\EntityDataModel\Type\Type\ResourceType;
use Rolab\EntityDataModel\Property\ResourceProperty;
use Rolab\EntityDataModel\Property\SimpleProperty;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;

abstract class StructuralType extends ResourceType
{
	private $className;
	
	private $name;
	
	private $namespace;
	
	protected $simpleProperties;
	
	private $baseType;
	
	public function __construct($className, $name, $namespace, array $properties = array(), StructuralType $baseType = null)
	{
		$this->simpleProperties = array();
		
		$this->className = $className;
		$this->name = $name;
		$this->namespace = $namespace;
		$this->setProperties($properties);
		$this->baseType = $baseType;
	}
	
	public function getClassName($className)
	{
		return $this->className;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function getNamespace()
	{
		return $this->namespace;
	}
	
	public function getFullName()
	{
		return isset($this->namespace) ?  $this->namespace .'.'. $this->name : $this->name;
	}
	
	public function getProperties()
	{
		return $this->getSimpleProperties();
	}
	
	public function getSimpleProperties()
	{
		return isset($this->baseType) ? array_merge($this->baseType->getSimpleProperties(), $this->simpleProperties) : 
			$this->simpleProperties;
	}
	
	public function setProperties(array $properties)
	{
		foreach ($properties as $property) {
			$this->addProperty($property);
		}
	}
	
	public function addProperty(ResourceProperty $property)
	{
		$this->addSimpleProperty($property);
	}
	
	public function addSimpleProperty(SimpleProperty $property)
	{
		$properties = $this->getProperties();
		
		if (isset($properties[$property->getName()])) {
			throw new InvalidArgumentException(sprintf('Type "%s" already has a property named "%s"',
				$this->getFullName(), $property->getName()));
		}
		
		$this->simpleProperties[$property->getName()] = $property;
	}
	
	public function removeProperty($propertyName)
	{
		unset($this->simpleProperties[$propertyName]);
	}
	
	public function getPropertyByName($propertyName)
	{
		$properties = $this->getProperties();
		
		return $properties[$propertyName];
	}
}
