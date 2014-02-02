<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\EntityDataModel\Type;

use Rolab\EntityDataModel\Type\ComplexType;
use Rolab\EntityDataModel\Type\ResourcePropertyDescription;
use Rolab\EntityDataModel\Type\StructuralPropertyDescription;
use Rolab\EntityDataModel\Type\NavigationPropertyDescription;
use Rolab\EntityDataModel\Type\KeyPropertyDescription;
use Rolab\EntityDataModel\Type\ETagPropertyDescription;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;

/**
 * Represents an entity type: a complex data type that is uniquely identifyable through
 * a key property or a combination of several partial key properties.
 * 
 * @author Roland Schermer <roland0507@gmail.com>
 */
class EntityType extends ComplexType
{
    /**
     * @var NavigationPropertyDescription[]
     */
    private $navigationPropertyDescriptions = array();
    
    /**
     * @var KeyPropertyDescription[]
     */
    private $keyPropertyDescriptions = array();
    
    /**
     * @var ETagPropertyDescription[]
     */
    private $eTagPropertyDescriptions = array();
    
    /**
     * @var EntityType
     */
    private $baseType;
    
    /**
     * @var boolean
     */
    private $isAbstract;
    
    /**
     * @var boolean
     */
    private $constructionCompleted = false;
    
    /**
     * Creates a new entity type.
     *
     * @param string                        $name                 The name of the complex type (may only
     *                                                            contain alphanumeric characters and the
     *                                                            underscore).
     * @param ReflectionClass               $reflection           Reflection of the class this structural
     *                                                            type maps to.
     * @param ResourcePropertyDescription[] $propertyDescriptions Descriptions for each of the properties.
     * @param EntityType                    $baseType             A base type this entity type extends and
     *                                                            inherits all properties from.
     * @param boolean                       $isAbstract           Whether or not this entity type can be
     *                                                            instantiated.
     * 
     * @throws InvalidArgumentException Thrown if the name contains illegal characters.
     *                                  Thrown if the property description list is empty.
     * 
     */
    public function __construct($name, \ReflectionClass $reflection, array $propertyDescriptions,
        EntityType $baseType = null, $isAbstract = false
    ) {
        parent::__construct($name, $reflection, $propertyDescriptions);

        if (empty($this->keyPropertyDescriptions) && null === $baseType) {
            throw new InvalidArgumentException(sprintf(
                'Entity type "%s" must be given either atleast one KeyPropertyDescription or ' .
                'a base entity type.', $this->getFullName()));
        } elseif (count($this->keyPropertyDescriptions) > 0 && null !== $baseType) {
            throw new InvalidArgumentException(sprintf(
                'Entity type "%s" may be given either a base entity type or one or more KeyPropertyDescriptions, ' .
                'but it may not be given both a base type and KeyPropertyDescriptions.', $this->getFullName()
            ));
        }

        $this->baseType = $baseType;
        $this->isAbstract = false;
        $this->constructionCompleted = true;
    }
    
    /**
     * Returns the base type this entity type extends and inherits all properties from.
     * 
     * @return EntityType The base type this entity type extends.
     */
    public function getBaseType()
    {
        return $this->baseType;
    }
    
    /**
     * Returns whether or not this entity type can be instantiated.
     * 
     * @return boolean Whether or not this entity type can be instantiated.
     */
    public function isAbstract()
    {
        return $this->isAbstract;
    }
    
    /**
     * {@inheritDoc}
     */
    public function addPropertyDescription(ResourcePropertyDescription $propertyDescription)
    {
        $propertyDescriptions = $this->getPropertyDescriptions();

        if (isset($propertyDescriptions[$propertyDescription->getName()])) {
            throw new InvalidArgumentException(sprintf('Type "%s" already has a property named "%s"',
                $this->getFullName(), $propertyDescription->getName()));
        }

        if ($this->constructionCompleted && $propertyDescription instanceof KeyPropertyDescription) {
            throw new InvalidArgumentException(sprintf(
                'Cannot add key properties after the initial construction of the entity type.',
                $this->getFullName(), $propertyDescription->getName()
            ));
        }

        if ($propertyDescription instanceof NavigationPropertyDescription) {
            $this->addNavigationPropertyDescription($propertyDescription);
        } elseif ($propertyDescription instanceof StructuralPropertyDescription) {
            $this->addStructuralPropertyDescription($propertyDescription);
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function removePropertyDescription($propertyDescriptionName)
    {
        if (isset($this->keyPropertyDescriptions[$propertyDescriptionName])) {
            throw new InvalidArgumentException('Cannot remove key properties from an entity type.');
        }

        parent::removePropertyDescription($propertyDescriptionName);

        unset($this->navigationPropertyDescriptions[$propertyDescriptionName]);
        unset($this->eTagPropertyDescriptions[$propertyDescriptionName]);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getPropertyDescriptions()
    {
        return array_merge($this->getStructuralPropertyDescriptions(), $this->getNavigationPropertyDescriptions());
    }
    
    /**
     * Returns whether or not this entity type has any e-tag properties.
     * 
     * @return boolean Whether or not this entity type has any e-tag properties.
     */
    public function hasETag()
    {
        return isset($this->eTagPropertyDescriptions);
    }
    
    /**
     * Returns all key property descriptions for this entity type.
     * 
     * @return KeyPropertyDescription[] All key property descriptions for this entity type.
     */
    public function getKeyPropertyDescriptions()
    {
        return isset($this->baseType) ?
            array_merge($this->baseType->getKeyPropertyDescriptions(), $this->keyPropertyDescriptions) :
            $this->keyPropertyDescriptions;
    }
    
    /**
     * Returns all e-tag property descriptions for this entity type.
     * 
     * @return ETagPropertyDescription[] All e-tag property descriptions for this entity type.
     */
    public function getETagPropertyDescriptions()
    {
        return isset($this->baseType) ?
            array_merge($this->baseType->getETagPropertyDescriptions(), $this->eTagPropertyDescriptions) :
            $this->eTagPropertyDescriptions;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getStructuralPropertyDescriptions()
    {
        return isset($this->baseType) ? array_merge(
                $this->baseType->getStructuralPropertyDescriptions(),
                parent::getStructuralPropertyDescriptions()
            ) : parent::getStructuralPropertyDescriptions();
    }
    
    /**
     * Returns all navigation property descriptions for this entity type.
     * 
     * @return NavigationPropertyDescription[] All navigation property descriptions for this
     *                                         entity type.
     */
    public function getNavigationPropertyDescriptions()
    {
        return isset($this->baseType) ?
            array_merge($this->baseType->getNavigationPropertyDescriptions(), $this->navigationPropertyDescriptions) :
            $this->navigationPropertyDescriptions;
    }

    protected function addStructuralPropertyDescription(StructuralPropertyDescription $propertyDescription)
    {
        parent::addStructuralPropertyDescription($propertyDescription);

        if ($propertyDescription instanceof KeyPropertyDescription) {
            $this->keyPropertyDescriptions[$propertyDescription->getName()] = $propertyDescription;
        }

        if ($propertyDescription instanceof ETagPropertyDescription) {
            $this->eTagPropertyDescriptions[$propertyDescription->getName()] = $propertyDescription;
        }
    }

    protected function addNavigationPropertyDescription(NavigationPropertyDescription $propertyDescription)
    {
        $this->navigationPropertyDescriptions[$propertyDescription->getName()] = $propertyDescription;
    }
}
