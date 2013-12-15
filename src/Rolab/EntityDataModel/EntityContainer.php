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

use Rolab\EntityDataModel\EntitySet;
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
class EntityContainer
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var EntityContainer
     */
    private $parentContainer;

    /**
     * @var EntityDataModel
     */
    private $entityDataModel;

    /**
     * @var array
     */
    private $entitySets = array();
    
    /**
     * @var array
     */
    private $associationSets = array();
    
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
    public function __construct($name, EntityContainer $parentContainer = null)
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) {
            throw new InvalidArgumentException(sprintf('"%s" is an illegal name for a container. The name for ' .
                'a container may only contain alphanumeric characters and underscores.', $name));
        }

        $this->name = $name;
        $this->parentContainer = $parentContainer;
    }
    
    /**
     * Sets the entity data model the container is a part of.
     * 
     * Sets the entity data model the container is a part of. An entity container should
     * always be part of some entity data model.
     * 
     * @param EntityDataModel $entityDataModel The entity data model the entity container is
     *                                         a part of.
     */
    public function setEntityDataModel(EntityDataModel $entityDataModel)
    {
        $this->entityDataModel = $entityDataModel;
    }
    
    /**
     * Returns the entity data model the entity container is a part of.
     * 
     * @return null|EntityDataModel The entity data model the entity container is a part of
     *                              or null if no entity data model is assigned yet.
     */
    public function getEntityDataModel()
    {
        return $this->entityDataModel;
    }
    
    /**
     * Returns the name of the entity container without namespace prefix.
     *
     * @return string The name of the entity container.
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Returns the full name of the entity container with namespace prefix.
     * 
     * Returns the name of the entity container with namespace prefix if an entity data
     * model was set or the name without a prefix if no entity data model was set.
     *
     * @return string The full name of the entity container.
     */
    public function getFullName()
    {
        return isset($this->entityDataModel) ? $this->entityDataModel->getNamespace() .'.'. $this->name : $this->name;
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
     * @return array An array container all entity sets in the entity containers.
     */
    public function getEntitySets()
    {
        return isset($this->parentContainer) ?
            array_merge($this->parentContainer->getEntitySets(), $this->entitySets) : $this->entitySets;
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
        $entitySets = isset($this->parentContainer) ?
            array_merge($this->parentContainer->getEntitySets(), $this->entitySets) : $this->entitySets;

        return isset($entitySets[$name]) ? $entitySets[$name] : null;
    }
    
    /**
     * Adds an association set to the entity container.
     * 
     * Adds an association set to the entity container. Association sets within one entity
     * container must have unique names. However, an association set may have the same
     * name as another association set in the parent container, in which case the assocation
     * set in the parent container is overriden.
     * 
     * @param EntitySet $entitySet The entity set to be added to the entity container
     * 
     * @throws InvalidArgumentException Thrown if an entity set is added with a name that
     *                                  is already in use by another entity set in the same
     *                                  entity container.
     */
    public function addAssociationSet(AssociationSet $associationSet)
    {
        if (isset($this->associationSets[$associationSet->getName()])) {
            throw new InvalidArgumentException(sprintf(
                'The entity container already contains an association set by the name "%s"',
                $associationSet->getName()
            ));
        }
        
        foreach ($associationSet->getSetEnds() as $setEnd) {
            if (null === $this->getEntitySetByName($setEnd->getEntitySet()->getName())) {
                throw new InvalidArgumentException(sprintf(
                    'The entity set end in the association set must point to entity sets in this container or ' .
                    'in the container\'s parent container.',
                    $associationSet->getName()
                ));
            }
        }

        $this->associationSets[$associationSet->getName()] = $associationSet;

        $associationSet->setEntityContainer($this);
    }
    
    /**
     * Returns all association sets in the current entity container.
     * 
     * Returns all association sets in the current entity container. If a container has a
     * parent container, association sets in the parent container will also be returned. If
     * an association set in the parent container has the same name as an association set
     * in the child container, only the assocation set in the child container will be
     * returned.
     * 
     * @return array An array container all entity sets in the entity containers.
     */
    public function getAssociationSets()
    {
        return isset($this->parentContainer) ?
            array_merge($this->parentContainer->getAssociationSets(), $this->associationSets) : $this->associationSets;
    }
    
    /**
     * Searches the entity container for an association set with a specific name.
     * 
     * Searches the entity container for an association set with a specific name and if an
     * association set with that name exists in either the container itself, or the parent
     * container, an association set will be returned. If the both the child container and
     * the parent container contain an association set with the same name, only the association
     * set in the child container will be returned.
     * 
     * @return null|AssociationSet An association set with the name searched for or null if no
     *                             such association set exists in the container.
     */
    public function getAssociationSetByName($associationSetName)
    {
        $associationSets = isset($this->parentContainer) ?
            array_merge($this->parentContainer->getAssociationSets(), $this->associationSets) : $this->associationSets;

        return isset($associationSets[$associationSetName]) ? $associationSets[$associationSetName] : null;
    }
}
