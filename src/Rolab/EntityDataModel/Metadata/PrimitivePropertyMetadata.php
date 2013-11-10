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

use Metadata\PropertyMetadata;

class PrimitivePropertyMetadata extends PropertyMetadata
{
    public $resourceType;

    public $isKey;

    public $isETag;

    public $isCollection;

    public function serialize()
    {
        return serialize(array(
            $this->class,
            $this->resourceType,
            $this->isKey,
            $this->isETag,
            $this->isCollection,
            $this->name,
        ));
    }

    public function unserialize($str)
    {
        list(
            $this->class,
            $this->resourceType,
            $this->isKey,
            $this->isETag,
            $this->isCollection,
            $this->name
        ) = unserialize($str);

        $this->reflection = new \ReflectionProperty($this->class, $this->name);
        $this->reflection->setAccessible(true);
    }
}
