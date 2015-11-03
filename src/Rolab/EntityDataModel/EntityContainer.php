<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel;

use Rolab\EntityDataModel\Exception\InvalidArgumentException;

/**
 * Defines a logical grouping of entities and their associations. Is used
 * in the OData protocol to determine accessibility and uri's for entity
 * resources. An entity container may have a parent container. Entity sets
 * and association sets in the child container may then references any of 
 * the entity sets and association sets in the parent container. 
 *
 * @author Roland Schermer <roland0507@gmail.com>
 */
class EntityContainer extends NamedModelElement
{
    /**
     * @var EntityContainer
     */
    private $parentContainer;

    /**
     * @var array
     */
    private $entitySets = array();
    
    /**
     * Creates a new entity container.
     * 
     * Creates a new entity container. A parent container may be specified, in which
     * case entity sets and association sets in the container may reference any of the
     * entity sets and association sets in the parent container.
     * 
     * @param string               $name            The name of the entity container (must contain 
     *                                              only alphanumber characters and underscores).
     * @param null|EntityContainer $parentContainer An optional parent container for the entity 
     *                                              container
     * 
     * @throws InvalidArgumentException Thrown if the container's name contains illegal characters.
     */
    public function __construct(string $name, EntityContainer $parentContainer = null)
    {
        parent::__construct($name);
        
        $this->parentContainer = $parentContainer;
    }
    
    /**
     * Returns the entity container's parent container if one was set.
     * 
     * @return EntityContainer The entity container's parent container.
     */
    public function getParentContainer()
    {
        return $this->parentContainer;
    }
    
    /**
     * Adds an entity set to the entity container.
     * 
     * Adds an entity set to the entity container. Entity sets within one entity
     * container must have unique names. However, an entity set may have the same
     * name as another entity set in the parent container, in which case the entity
     * set in the parent container is overriden.
     * 
     * @param EntitySet $entitySet The entity set to be added to the entity container
     * 
     * @throws InvalidArgumentException Thrown if an entity set is added with a name that
     *                                  is already in use by another entity set in the same
     *                                  entity container.
     */
    public function addEntitySet(EntitySet $entitySet)
    {
        if (isset($this->entitySets[$entitySet->getName()])) {
            throw new InvalidArgumentException(sprintf(
                'The entity container already contains an entity set by the name "%s"',
                $entitySet->getName()
            ));
        }

        $this->entitySets[$entitySet->getName()] = $entitySet;

        $entitySet->setEntityContainer($this);
    }
    
    /**
     * Returns all entity sets in the current entity container.
     * 
     * Returns all entity sets in the current entity container. If a container has a
     * parent container, entity sets in the parent container will also be returned. If
     * an entity set in the parent container has the same name as an entity set in the
     * child container, only the entity set in the child container will be returned.
     * 
     * @return EntitySet[] An array container all entity sets in the entity containers.
     */
    public function getEntitySets() : array
    {
        if (isset($this->parentContainer)) {
            return array_merge($this->parentContainer->getEntitySets(), $this->entitySets);
        }

        return $this->entitySets;
    }
    
    /**
     * Searches the entity container for an entity set with a specific name.
     * 
     * Searches the entity container for an entity set with a specific name and if an
     * entity set with that name exists in either the container itself, or the parent
     * container, an entity set will be returned. If the both the child container and
     * the parent container contain an entity set with the same name, only the entity
     * set in the child container will be returned.
     * 
     * @return null|EntitySet An entity set with the name searched for or null if no
     *                        such entity set exists in the container.
     */
    public function getEntitySetByName($name)
    {
        $entitySets = $this->getEntitySets();

        return isset($entitySets[$name]) ? $entitySets[$name] : null;
    }
}
