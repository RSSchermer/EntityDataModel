<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\EntityDataModel\Metadata;

use Metadata\MergeableClassMetadata;

class ClassMetadata extends MergeableClassMetadata
{
	public $typeName;
	
	public $typeNamespace;
	
	public $setName;
	
	public function serialize()
    {
        return serialize(array(
            $this->name,
            $this->typeName,
            $this->typeNamespace,
            $this->setName,
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
            $this->setName,
            $this->methodMetadata,
            $this->propertyMetadata,
            $this->fileResources,
            $this->createdAt
        ) = unserialize($str);

        $this->reflection = new \ReflectionClass($this->name);
    }
}
