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

use Rolab\EntityDataModel\EntityContainer;
use Rolab\EntityDataModel\Type\EntityType;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;

/**
 * Represents a set of entity instances of a certain entity type.
 *
 * @author Roland Schermer <roland0507@gmail.com>
 */
class EntitySet
{
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var EntityType
     */
    private $entityType;
    
    /**
     * @var EntityContainer
     */
    private $entityContainer;
    
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
        if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) {
            throw new InvalidArgumentException(sprintf('"%s" is an illegal name for an entity set. The name for ' .
                'an entity set may only contain alphanumeric characters and underscores.', $name));
        }

        $this->name = $name;
        $this->entityType = $entityType;
    }
    
    /**
     * Sets the entity container the entity set is contained in.
     * 
     * Sets the entity container the entity set is contained in. An entity set should
     * always be part of some entity container.
     * 
     * @param EntityContainer $entityContainer The entity container the entity set is a part of.
     */
    public function setEntityContainer(EntityContainer $entityContainer)
    {
        $this->entityContainer = $entityContainer;
    }
    
    /**
     * Returns the entity container the entity set is contained in.
     * 
     * @return null|EntityContainer The entity container the entity set is contained in or null if
     *                              no entity container was set.
     */
    public function getEntityContainer()
    {
        return $this->entityContainer;
    }
    
    /**
     * Returns the name of the entity set.
     * 
     * @return string The name of the entity set.
     */
    public function getName()
    {
        return $this->name;
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
