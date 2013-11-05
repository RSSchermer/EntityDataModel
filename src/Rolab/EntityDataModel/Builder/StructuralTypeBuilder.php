<?php

namespace Rolab\EntityDataModel\Builder;

class StructuralTypeBuilder
{
	private $entityDataModelBuilder;
	
	public function __construct(EntityDataModelBuilder $entityDataModelBuilder)
	{
		$this->entityDataModelBuilder = $entityDataModelBuilder;
	}
	
	public function entityType($className, $name, $namespace, $baseType = null)
	{
		$entityTypeDefinition = new EntityTypeDefition($className, $name, $namespace, $baseType);
		$entityTypeDefinition->setStructuralTypeBuilder($this);
		
		return $entityTypeDefinition;
	}
	
	public function complexType($className, $name, $namespace, $baseType = null)
	{
		$complexTypeDefinition = new ComplexTypeDefition($className, $name, $namespace, $baseType);
		$complexTypeDefinition->setStructuralTypeBuilder($this);
		
		return $entityTypeDefinition;
	}
	
	public function addStructuralType(StructuralType $structuralType)
	{
		$this->entityDataModelBuilder->addStructuralType($structuralType);
	}
	
	public function end()
	{
		return $this->entityDataModelBuilder;
	}
}
