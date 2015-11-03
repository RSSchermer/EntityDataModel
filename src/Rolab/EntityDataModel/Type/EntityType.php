<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Type;

use Rolab\EntityDataModel\Exception\InvalidArgumentException;

/**
 * Represents an entity type: a complex data type that is uniquely identifiable through
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
     * @param string                           $name                           The name of the complex type (may only
     *                                                                         contain alphanumeric characters and the
     *                                                                         underscore).
     * @param \ReflectionClass                 $reflection                     Reflection of the class this structural
     *                                                                         type maps to.
     * @param StructuralPropertyDescription[]  $structuralPropertyDescriptions Descriptions for each of the structural
     *                                                                         properties.
     * @param NavigationPropertyDescription[]  $navigationPropertyDescriptions Descriptions for each of the navigation
     *                                                                         properties.
     * @param EntityType                       $baseType                       A base type this entity type extends and
     *                                                                         inherits all properties from.
     * @param bool                             $isAbstract                     Whether or not this entity type can be
     *                                                                         instantiated.
     *
     * @throws InvalidArgumentException Thrown if the name contains illegal characters.
     *                                  Thrown if the property description list is empty.
     *
     */
    public function __construct(
        string $name,
        \ReflectionClass $reflection,
        array $structuralPropertyDescriptions,
        array $navigationPropertyDescriptions = array(),
        EntityType $baseType = null,
        bool $isAbstract = false
    ) {
        parent::__construct($name, $reflection, $structuralPropertyDescriptions);

        if (empty($this->keyPropertyDescriptions) && null === $baseType) {
            throw new InvalidArgumentException(sprintf(
                'Entity type "%s" must be given either at least one KeyPropertyDescription or a base entity type.',
                $this->getFullName()
            ));
        } elseif (count($this->keyPropertyDescriptions) > 0 && null !== $baseType) {
            throw new InvalidArgumentException(sprintf(
                'Entity type "%s" may be given either a base entity type or one or more KeyPropertyDescriptions, ' .
                'but it may not be given both a base type and KeyPropertyDescriptions.',
                $this->getFullName()
            ));
        }

        foreach ($navigationPropertyDescriptions as $propertyDescription) {
            $this->addNavigationPropertyDescription($propertyDescription);
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
    public function getBaseType() : EntityType
    {
        return $this->baseType;
    }
    
    /**
     * Returns whether or not this entity type can be instantiated.
     * 
     * @return boolean Whether or not this entity type can be instantiated.
     */
    public function isAbstract() : bool
    {
        return $this->isAbstract;
    }
    
    /**
     * {@inheritDoc}
     */
    public function addStructuralPropertyDescription(StructuralPropertyDescription $propertyDescription)
    {
        if ($this->constructionCompleted && $propertyDescription instanceof KeyPropertyDescription) {
            throw new InvalidArgumentException(sprintf(
                'Cannot add key properties after the initial construction of the entity type.',
                $this->getFullName(),
                $propertyDescription->getName()
            ));
        }

        parent::addStructuralPropertyDescription($propertyDescription);

        if ($propertyDescription instanceof KeyPropertyDescription) {
            $this->keyPropertyDescriptions[$propertyDescription->getName()] = $propertyDescription;
        }

        if ($propertyDescription instanceof ETagPropertyDescription) {
            $this->eTagPropertyDescriptions[$propertyDescription->getName()] = $propertyDescription;
        }
    }

    public function addNavigationPropertyDescription(NavigationPropertyDescription $propertyDescription)
    {
        if (isset($propertyDescriptions[$propertyDescription->getName()])) {
            throw new InvalidArgumentException(sprintf(
                'Type "%s" already has a property named "%s"',
                $this->getFullName(),
                $propertyDescription->getName()
            ));
        }

        $this->navigationPropertyDescriptions[$propertyDescription->getName()] = $propertyDescription;
        $propertyDescription->setStructuredType($this);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getPropertyDescriptions() : array
    {
        return array_merge($this->getStructuralPropertyDescriptions(), $this->getNavigationPropertyDescriptions());
    }

    /**
     * Returns whether or not this entity type has any e-tag properties.
     *
     * @return bool Whether or not this entity type has any e-tag properties.
     */
    public function hasETag() : bool
    {
        return isset($this->eTagPropertyDescriptions);
    }
    
    /**
     * Returns all key property descriptions for this entity type.
     * 
     * @return KeyPropertyDescription[] All key property descriptions for this entity type.
     */
    public function getKeyPropertyDescriptions() : array
    {
        if(isset($this->baseType)) {
            return array_merge($this->baseType->getKeyPropertyDescriptions(), $this->keyPropertyDescriptions);
        }

        return $this->keyPropertyDescriptions;
    }
    
    /**
     * Returns all e-tag property descriptions for this entity type.
     * 
     * @return ETagPropertyDescription[] All e-tag property descriptions for this entity type.
     */
    public function getETagPropertyDescriptions() : array
    {
        if(isset($this->baseType)) {
            return array_merge($this->baseType->getETagPropertyDescriptions(), $this->eTagPropertyDescriptions);
        }

        return $this->eTagPropertyDescriptions;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getStructuralPropertyDescriptions() : array
    {
        if(isset($this->baseType)) {
            return array_merge(
                $this->baseType->getStructuralPropertyDescriptions(),
                parent::getStructuralPropertyDescriptions()
            );
        }

        return parent::getStructuralPropertyDescriptions();
    }
    
    /**
     * Returns all navigation property descriptions for this entity type.
     * 
     * @return NavigationPropertyDescription[] All navigation property descriptions for this
     *                                         entity type.
     */
    public function getNavigationPropertyDescriptions() : array
    {
        if(isset($this->baseType)) {
            return array_merge(
                $this->baseType->getNavigationPropertyDescriptions(),
                $this->navigationPropertyDescriptions
            );
        }

        return $this->navigationPropertyDescriptions;
    }
}
