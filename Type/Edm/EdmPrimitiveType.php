<?php

namespace Rolab\EntityDataModel\Type\Edm;

use Rolab\EntityDataModel\Type\PrimitiveType;

abstract class EdmPrimitiveType extends PrimitiveType
{
	public function getNamespace()
	{
		return 'Edm';
	}
	
	public function getFullName()
	{
		return 'Edm.'. $this->getName();
	}
}
