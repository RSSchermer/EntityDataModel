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

use Rolab\EntityDataModel\Type\ComplexType;
use Rolab\EntityDataModel\Property\RegularProperty;
use Rolab\EntityDataModel\Property\NavigationProperty;
use Rolab\EntityDataModel\Property\KeyProperty;
use Rolab\EntityDataModel\Property\ETagProperty;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;

class EntityType extends ComplexType
{
	private $navigationProperties;
	
	private $keyProperties;
	
	private $eTagProperties;
	
	public function __construct($className, $name, $namespace, array $properties, ComplexType $baseType = null)
	{
		$this->navigationProperties = array();
		$this->keyProperties = array();
		$this->eTagProperties = array();
		
		parent::__construct($className, $name, $namespace, $properties, $baseType);
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
			throw new InvalidArgumentException(sprintf('Entity type "%s" must be given atleast one property of type ' .
				'\Rolab\EntityDataModel\Property\KeyProperty', $this->getFullName()));
		}
	}
	
	public function getProperties()
	{
		return array_merge($this->getRegularProperties(), $this->getNavigationProperties());
	}
	
	public function addProperty(ResourceProperty $property)
	{
		if (isset($this->properties[$property->getName()])) {
			throw new InvalidArgumentException(sprintf('Type "%s" already has a property named "%s"',
				$this->getFullName(), $property->getName()));
		}
		
		if ($property instanceof NavigationProperty) {
			$this->addNavigationProperty($property);
		} elseif ($property instanceof RegularProperty) {
			$this->addRegularProperty($property);
		}
		
		if ($property instanceof KeyProperty) {
			$this->keyProperties[$property->getName()] = $property;
		}
		
		if ($property instanceof ETagProperty) {
			$this->eTagProperties[$property->getName()] = $property;
		}
	}
	
	public function addNavigationProperty(NavigationProperty $property)
	{
		$properties = $this->getProperties();
		
		if (isset($properties[$property->getName()])) {
			throw new InvalidArgumentException(sprintf('Type "%s" already has a property named "%s"',
				$this->getFullName(), $property->getName()));
		}
		
		$this->navigationProperties[$property->getName()] = $property;
	}
	
	public function getNavigationProperties()
	{
		return $this->navigationProperties;
	}
	
	public function removeProperty($propertyName)
	{
		parent::removeProperty($propertyName);
		
		unset($this->navigationProperties[$propertyName]);
		unset($this->keyProperties[$propertyName]);
		unset($this->eTagProperties[$propertyName]);
		
		if (count($this->keyProperties) === 0) {
			throw new InvalidArgumentException(sprintf('Entity type "%s" must keep atleast one property of type ' .
				'\Rolab\EntityDataModel\Property\KeyProperty', $this->getFullName()));
		}
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
