<?php

namespace Rolab\ODataProducer\Model\Type;

abstract class ResourceType
{
	abstract public function getName();
	
	abstract public function getNamespace();
	
	abstract public function getFullName();
}
