<?php

namespace Rolab\EntityDataModel\Property;

use Rolab\EntityDataModel\Property\ResourceProperty;
use Rolab\EntityDataModel\Type\PrimitiveType;

class PrimitiveProperty extends ResourceProperty
{
	public function __construct($name, PrimitiveType $type)
	{
		parent::__construct($name, $type);
	}
}
