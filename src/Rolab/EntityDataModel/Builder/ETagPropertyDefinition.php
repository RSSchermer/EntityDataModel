<?php

namespace Rolab\EntityDataModel\Builder;

use Rolab\EntityDataModel\Builder\PrimitivePropertyDefition;

class ETagPropertyDefinition extends PrimitivePropertyDefition
{
	public function __construct($name, $typeName)
	{
		parent::__construct($name, $typeName, false);
	}
}
