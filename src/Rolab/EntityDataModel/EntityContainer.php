<?php

namespace Rolab\EntityDataModel;

use Rolab\EntityDataModel\Type\EntityType;
use Rolab\EntityDataModel\EntitySet;

class EntityContainer
{
	private $name;
	
	private $namespace;
	
	private $entitySets;
	
	private $parentContainer;
	
	public function __construct($name, $namespace, EntityContainer $parentContainer = null)
	{
		$this->name = $name;
		$this->namespace = $namespace;
		$this->parentContainer = $parentContainer;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function getNamespace()
	{
		return $this->namespace;
	}
	
	public function getParentContainer()
	{
		return $this->parentContainer;
	}
	
	public function setParentContainer(EntityContainer $parentContainer)
	{
		$this->parentContainer = $parentContainer;
	}
	
	public function getEntitySets()
	{
		return isset($this->parentContainer) ? array_merge($this->parentContainer->getEntitySets(), $this->entitySets)
			: $this->entitySets;
	}
	
	public function addEntitySet($entitySetName, EntityType $entityType)
	{
		$this->entitySets[$entitySetName] =  new EntitySet($entitySetName, $entityType, $this);
	}
	
	public function removeEntitySet($entitySetName)
	{
		unset($this->entitySets[$entitySetName]);
	}
	
	public function getEntitySetByName($name)
	{
		$entitySets = isset($this->parentContainer) ? array_merge($this->parentContainer->getEntitySets(), $this->entitySets)
			: $this->entitySets;
		return $entitySets[$name];
	}
}
