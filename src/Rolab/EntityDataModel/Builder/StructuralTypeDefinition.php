<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\EntityDataModel\Builder;

use Rolab\EntityDataModel\Builder\EntityDataModelBuilder;
use Rolab\EntityDataModel\Builder\PropertyDefinition;
use Rolab\EntityDataModel\Builder\PrimitivePropertyDefition;
use Rolab\EntityDataModel\Builder\ComplexPropertyDefition;

abstract class StructuralTypeDefinition
{
	private $className;
	
	private $name;
	
	private $namespace;
	
	private $baseTypeName;
	
	private $regularPropertyDefinitions;
	
	private $entityDataModelBuilder;
	
	public function __construct($className, $name, $namespace = null, $baseTypeName = null)
	{
		$this->className = $className;
		$this->name = $name;
		$this->namespace = $namespace;
		$this->baseTypeName = $baseTypeName;
		$this->regularPropertyDefinitions = array();
	}
	
	public function setEntityDataModelBuilder(EntityDataModelBuilder $entityDataModelBuilder)
	{
		$this->entityDataModelBuilder = $entityDataModelBuilder;
	}
	
	public function primitiveProperty($name, $type, $isCollection = false)
	{
		$this->addRegularPropertyDefinition(new PrimitivePropertyDefinition($name, $type, $isCollection));
	}
	
	public function complexProperty($name, $type, $isCollection = false)
	{
		$this->addRegularPropertyDefinition(new ComplexPropertyDefinition($name, $type, $isCollection));
	}
	
	public function addRegularPropertyDefinition(PropertyDefinition $property)
	{
		$this->regularPropertyDefinitions[] = $property;
	}
	
	public function getClassName()
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
	
	public function getBaseTypeName()
	{
		return $this->baseTypeName;
	}
	
	public function getPropertyDefinitions()
	{
		return $this->getRegularPropertyDefinitions();
	}
	
	public function getRegularPropertyDefinitions()
	{
		return $this->regularPropertyDefinitions;
	}
	
	public function end()
	{
		$this->entityDataModelBuilder->addStructuralType($this);
		
		return $this->entityDataModelBuilder;
	}
}
