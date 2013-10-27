<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\EntityDataModel;

use Rolab\EntityDataModel\Type\EntityType;
use Rolab\EntityDataModel\EntitySet;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;

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
		if (isset($this->entitySets[$entitySetName])) {
			throw new InvalidArgumentException(sprintf('The entity container already contains an entity set by the name "%s"', 
				$entitySetName));
		}
		
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
