<?php

namespace RSSchermer\EntityModel\ClassMetadata;

class PrimitivePropertyMetadata extends AbstractResourcePropertyMetadata
{
    public $primitiveTypeName;

    public $partOfKey = false;

    public function serialize()
    {
        return serialize(array(
            $this->primitiveTypeName,
            $this->partOfKey,
            parent::serialize()
        ));
    }

    public function unserialize($data)
    {
        list(
            $this->primitiveTypeName,
            $this->partOfKey,
            $parentData
        ) = unserialize($data);

        parent::unserialize($parentData);
    }
}
