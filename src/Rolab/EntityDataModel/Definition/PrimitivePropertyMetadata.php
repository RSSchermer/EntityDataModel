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

use Rolab\EntityDataModel\Definition\StructuralPropertyMetadata;

abstract class PrimitivePropertyMetadata extends StructuralPropertyMetadata
{
    public $type;
    
    public $isKey = false;
    
    public $isETag = false;
    
    public function serialize()
    {
        return serialize(array(
            $this->type,
            $this->isKey,
            $this->isETag,
            parent::serialize()
        ));
    }
    
    public function unserialize($data)
    {
        list(
            $this->type,
            $this->isKey,
            $this->isETag,
            $parentData
        ) = unserialize($data);
        
        parent::unserialize($parentData);
    }
}
