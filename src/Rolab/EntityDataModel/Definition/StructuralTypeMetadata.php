<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\EntityDataModel\Definition;

use Metadata\MergeableClassMetadata;

abstract class StructuralTypeMetadata extends MergeableClassMetadata
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
