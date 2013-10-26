<?php

namespace Rolab\EntityDataModel\Metadata;

use Metadata\MergeableClassMetadata;

class ClassMetadata extends MergeableClassMetadata
{
	public $typeName;
	
	public $typeNamespace;
	
	public function serialize()
    {
        return serialize(array(
            $this->name,
            $this->typeName,
            $this->typeNamespace,
            $this->methodMetadata,
            $this->propertyMetadata,
            $this->fileResources,
            $this->createdAt,
        ));
    }

    public function unserialize($str)
    {
        list(
            $this->name,
            $this->typeName,
            $this->typeNamespace,
            $this->methodMetadata,
            $this->propertyMetadata,
            $this->fileResources,
            $this->createdAt
        ) = unserialize($str);

        $this->reflection = new \ReflectionClass($this->name);
    }
}
