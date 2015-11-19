<?php

namespace RSSchermer\EntityModel\ClassMetadata;

use Metadata\MergeableClassMetadata;

abstract class AbstractStructuredTypeMetadata extends MergeableClassMetadata
{
    public $typeName;

    public function serialize()
    {
        return serialize(array(
            $this->typeName,
            parent::serialize()
        ));
    }

    public function unserialize($data)
    {
        list(
            $this->typeName,
            $parentData
        ) = unserialize($data);

        parent::unserialize($parentData);
    }
}
