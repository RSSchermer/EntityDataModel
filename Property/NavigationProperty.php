<?php

namespace Rolab\EntityDataModel\Property;

use Rolab\EntityDataModel\Property\ResourceProperty;
use Rolab\EntityDataModel\Type\EntityType;
use Rolab\EntityDataModel\EntitySet;

abstract class NavigationProperty extends ResourceProperty
{
	private $targetEntitySet;
	
	public function __construct($name, EntitySet $targetEntitySet)
	{
		parent::__construct($name, $targetEntitySet->getType());
		
		$this->targetEntitySet = $targetEntitySet;
	}
	
	public function getTargetEntitySet()
	{
		return $this->targetEntitySet;
	}
}
