<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\EntityDataModel;

use Rolab\EntityDataModel\NamedContainerElement;
use Rolab\EntityDataModel\Type\EntityType;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;

/**
 * Represents a set of entity instances of a certain entity type.
 *
 * @author Roland Schermer <roland0507@gmail.com>
 */
class EntitySet extends NamedContainerElement
{
    /**
     * @var EntityType
     */
    private $entityType;

    /**
     * Creates a new entity set.
     * 
     * @param string     $name       The name of the entity set (must contain only alphanumber
     *                               characters and underscores).
     * @param EntityType $entityType The entity type that the entities this set represents are
     *                               instances of.
     * 
     * @throws InvalidArgumentException Thrown if the container's name contains illegal characters.
     */
    public function __construct($name, EntityType $entityType)
    {
        parent::__construct($name);
        
        $this->entityType = $entityType;
    }
    
    /**
     * Returns the entity type that all entities represented by the entity set are instances of.
     * 
     * @return EntityType The entity type that all entities represented by the entity set are
     *                    instances of.
     */
    public function getEntityType()
    {
        return $this->entityType;
    }
}
