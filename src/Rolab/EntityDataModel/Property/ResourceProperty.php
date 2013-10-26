<?php

namespace Rolab\EntityDataModel\Property;

use Rolab\EntityDataModel\Type\ResourceType;

abstract class ResourceProperty
{
	private $name;
	
	private $resourceType;
	
	public function __construct($name, ResourceType $resourceType)
	{
		$this->name = $name;
		$this->resourceType = $resourceType;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function getResourceType()
	{
		return $this->resourceType;
	}
}
