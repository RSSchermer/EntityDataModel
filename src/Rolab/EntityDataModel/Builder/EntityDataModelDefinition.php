<?php

namespace Rolab\EntityDataModel\Builder;

class EntityDataModelDefinition
{
	private $structuralTypeDefinitions;
	
	private $entityContainerDefinitions;
	
	private $defaultContainerName;
	
	public function __construct()
	{
		$this->structuralTypeDefinitions = array();
		$this->entityContainerDefinitions = array();
	}
	
	public function addStructuralTypeDefinition(StructuralTypeDefinition $structuralTypeDefinition)
	{
		$this->structuralTypeDefinition[] = $structuralTypeDefinition;
	}
	
	public function getStructuralTypeDefinitions()
	{
		return $this->structuralTypeDefinitions;
	}
	
	public function addEntityContainerDefinition(EntityContainerDefinition $entityContainerDefinition)
	{
		$this->entityContainerDefinitions[] = $entityContainerDefinition;
	}
	
	public function getEntityContainerDefinitions()
	{
		return $this->entityContainerDefinitions;
	}
	
	public function setDefaultContainer($containerName)
	{
		$this->defaultContainerName = $containerName;
	}
	
	public function getDefaultContainer()
	{
		return $this->defaultContainerName;
	}
}
