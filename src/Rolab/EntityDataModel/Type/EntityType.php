<?php

namespace Rolab\EntityDataModel\Type;

use Rolab\EntityDataModel\Type\ComplexType;

class EntityType extends ComplexType
{
	private $keyProperties;
	
	private $eTagProperties;
	
	public function __construct($name, $namespace, array $properties)
	{
		parent::__construct($name, $namespace, $properties);
	}
	
	public function setProperties(array $properties)
	{
		$hasKey = false;
		foreach ($properties as $property) {
			$this->addProperty($property);
			
			if ($property instanceof KeyProperty) {
				$hasKey = true;
			}
		}
		
		if (!$hasKey) {
			throw new \InvalidArgumentException(sprintf('Entity type "%s" must be given atleast one property of type ' .
				'\Rolab\EntityDataModel\Property\KeyProperty', $this->getFullName()));
		}
	}
	
	public function addProperty(ResourceProperty $property)
	{
		if (isset($this->properties[$property->getName()])) {
			throw new \InvalidArgumentException(sprintf('Type "%s" already has a property named "%s"',
				$this->getFullName(), $property->getName()));
		}
		
		$this->properties[$property->getName()] = $property;
		
		if ($property instanceof KeyProperty) {
			$this->keyProperties[$property->getName()] = $property;
		}
		
		if ($property instanceof ETagProperty) {
			$this->eTagProperties[$property->getName()] = $property;
		}
	}
	
	public function removeProperty($propertyName)
	{
		unset($this->properties[$propertyName]);
		unset($this->keyProperties[$propertyName]);
		unset($this->eTagProperties[$propertyName]);
	}
	
	public function getKeyProperties()
	{
		return $this->keyProperties;
	}
	
	public function hasETag()
	{
		return isset($this->eTagProperties);
	}
	
	public function getETagProperties()
	{
		return $this->eTagProperties;
	}
}
