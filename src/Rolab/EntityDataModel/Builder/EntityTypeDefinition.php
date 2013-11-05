<?php

namespace Rolab\EntityDataModel\Builder;

use Rolab\EntityDataModel\Builder\ComplexTypeDefinition;

class EntityTypeDefinition extends ComplexTypeDefinition
{
	private $navigationPropertyDefinitions;
	
	public function __construct()
	{
		
	}
	
	public function keyProperty($name, $typeName)
	{
		$this->addProperty(new KeyPropertyDefinition($name, $typeName));
	}
	
	public function eTagProperty($name, $typeName)
	{
		$this->addProperty(new ETagPropertyDefinition($name, $typeName));
	}
	
	public function navigationProperty($name, $setName, $isCollection = false)
	{
		$this->addProperty(new NavigationPropertyDefinition($name, $setName, $isCollection));
	}
	
	public function addNavigationPropertyDefinition(NavigationPropertyDefinition $navigationPropertyDefinition)
	{
		
	}
	
	public function getNavigationPropertyDefinitions()
	{
		return $this->navigationPropertyDefinitions;
	}
}
