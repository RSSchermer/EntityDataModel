<?php

namespace Rolab\EntityDataModel\Metadata;

use Metadata\PropertyMetadata;

class NavigationPropertyMetadata extends PropertyMetadata
{
	public $targetClass;
	
	public $isEntityReference;
	
	public $isEntitySetReference;
	
	public $isBag;
	
	public function serialize()
    {
        return serialize(array(
            $this->class,
            $this->targetClass,
            $this->isEntityReference,
            $this->isEntitySetReference,
            $this->isBag,
            $this->name,
        ));
    }

    public function unserialize($str)
    {
        list(
        	$this->class,
        	$this->targetClass,
        	$this->isEntityReference,
            $this->isEntitySetReference,
            $this->isBag,
        	$this->name
		) = unserialize($str);

        $this->reflection = new \ReflectionProperty($this->class, $this->name);
        $this->reflection->setAccessible(true);
    }
}
	