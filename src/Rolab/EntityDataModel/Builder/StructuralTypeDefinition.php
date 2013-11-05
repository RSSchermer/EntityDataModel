<?php

namespace Rolab\EntityDataModel\Builder;

use Rolab\EntityDataModel\Builder\StructuralTypeBuilder;

abstract class StructuralTypeDefinition
{
	private $className;
	
	private $name;
	
	private $namespace;
	
	private $baseTypeName;
	
	private $propertyDefinitions;
	
	private $structuralTypeBuilder;
	
	public function __construct($className, $name, $namespace = null, $baseTypeName = null)
	{
		$this->className = $className;
		$this->name = $name;
		$this->namespace = $namespace;
		$this->baseTypeName = $baseTypeName;
		$this->propertyDefinitions = array();
	}
	
	public function setStructuralTypeBuilder(StructuralTypeBuilder $structuralTypeBuilder)
	{
		$this->structuralTypeBuilder = $structuralTypeBuilder;
	}
	
	public function primitiveProperty($name, $type, $isCollection = false)
	{
		$this->addPropertyDefinition(new PrimitivePropertyDefinition($name, $type, $isCollection));
	}
	
	public function complexProperty($name, $type, $isCollection = false)
	{
		$this->addPropertyDefinition(new ComplexPropertyDefinition($name, $type, $isCollection));
	}
	
	public function addPropertyDefinition(PropertyDefinition $property)
	{
		$this->propertyDefinitions[] = $property;
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
	
	public function getBaseType()
	{
		return $this->baseType;
	}
	
	public function getPropertyDefinitions()
	{
		return $this->propertyDefinitions;
	}
	
	public function end()
	{
		$this->structuralTypeBuilder->addStructuralType($this);
		
		return $this->structuralTypeBuilder;
	}
}
