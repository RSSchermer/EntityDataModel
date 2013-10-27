<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\ODataProducer\Model\Type;

use Rolab\ODataProducer\Model\Type\ResourceType;

class ComplexType extends ResourceType
{
	private $className;
	
	private $name;
	
	private $namespace;
	
	protected $properties;
	
	public function __construct($className, $name, $namespace, array $properties = array())
	{
		$this->className = $className;
		$this->name = $name;
		$this->namespace = $namespace;
		$this->setProperties($properties);
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
		return $this->properties;
	}
	
	public function setProperties(array $properties)
	{
		foreach ($properties as $property) {
			$this->addProperty($property);
		}
	}
	
	public function addProperty(ResourceProperty $property)
	{
		if (isset($this->properties[$property->getName()])) {
			throw new \InvalidArgumentException(sprintf('Type "%s" already has a property named "%s"',
				$this->getFullName(), $property->getName()));
		}
		
		$this->properties[$property->getName()] = $property;
	}
	
	public function removeProperty($propertyName)
	{
		unset($this->properties[$propertyName]);
	}
	
	public function getPropertyByName($propertyName)
	{
		return isset($this->properties[$propertyName]) ? $this->properties[$propertyName] : null;
	}
}
