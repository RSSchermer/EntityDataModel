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

use Rolab\EntityDataModel\Definition\StructuralTypeMetadata;

class EntityTypeMetadata extends StructuralTypeMetadata
{
    public $baseType;
    
    public $isAbstract = false;
    
    public function serialize()
    {
        return serialize(array(
            $this->baseType,
            $this->isAbstract,
            parent::serialize()
        ));
    }
    
    public function unserialize($data)
    {
        list(
            $this->baseType,
            $this->isAbstract,
            $parentData
        ) = unserialize($data);
        
        parent::unserialize($parentData);
    }
}
