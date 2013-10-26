<?php

namespace Rolab\EntityDataModel\Property;

use Rolab\EntityDataModel\Property\ResourceProperty;
use Rolab\EntityDataModel\Type\ComplexType;

class ComplexProperty extends ResourceProperty
{
	public function __construct($name, ComplexType $type)
	{
		parent::__construct($name, $type);
	}
}
