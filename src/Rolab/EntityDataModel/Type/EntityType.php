<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Type;

use Rolab\EntityDataModel\Exception\InvalidArgumentException;

/**
 * Represents an entity type.
 *
 * Entity types are structured types, for which one or more structural
 * properties define a key by which instances of this entity type can be
 * uniquely referenced. An entity type must either define one or more key
 * properties itself, or may inherit key properties from a base entity type.
 * An example would be a "Customer" type which, among other structural
 * properties, defines an "Id" property by which a customer instance is
 * uniquely identifiable.
 *
 * Apart from structural properties, an entity type can also define
 * navigation properties to specify relationships with other entity types.
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
     * @var PrimitivePropertyDescription[]
     */
    private $keyPropertyDescriptions = array();

    /**
     * @var PrimitivePropertyDescription[]
     */
    private $eTagPropertyDescriptions = array();

    /**
     * @var EntityType
     */
    private $baseType;

    /**
     * Creates a new entity type.
     *
     * @param string $name                                                    The name of the complex type (may only
     *                                                                        contain alphanumeric characters and the
     *                                                                        underscore).
     * @param \ReflectionClass $reflection                                    Reflection of the class this structural
     *                                                                        type maps to.
     * @param StructuralPropertyDescription[] $structuralPropertyDescriptions Descriptions for each of the structural
     *                                                                        properties.
     * @param NavigationPropertyDescription[] $navigationPropertyDescriptions Descriptions for each of the navigation
     *                                                                        properties.
     * @param EntityType $baseType                                            A base type this entity type extends and
     *                                                                        inherits all properties from.
     *
     * @throws InvalidArgumentException Thrown if the name contains illegal characters.
     *                                  Thrown if the property description list is empty.
     *
     */
    public function __construct(
        string $name,
        \ReflectionClass $reflection,
        array $structuralPropertyDescriptions,
        EntityType $baseType = null
    ) {
        parent::__construct($name, $reflection, $structuralPropertyDescriptions);

        if (empty($this->keyPropertyDescriptions) && null === $baseType) {
            throw new InvalidArgumentException(sprintf(
                'Tried to define entity type "%s" without at least one key property and without a base entity type. ' .
                'An entity type must be defined with either at least one key property or a base entity type.',
                $this->getFullName()
            ));
        } elseif (count($this->keyPropertyDescriptions) > 0 && null !== $baseType) {
            throw new InvalidArgumentException(sprintf(
                'Tried to define entity type "%s" with both a base entity type and one or more key properties. ' .
                'An entity type may either be defined with a base entity type or with one or more key properties, ' .
                'not both.',
                $this->getFullName()
            ));
        }

        $this->baseType = $baseType;
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
     * Returns whether or not this entity type is a subtype of the given entity type.
     *
     * This also returns true when given entity type is the same type as this entity type.
     *
     * @param EntityType $entityType The entity type for which to check if this entity type
     *                               is a subtype.
     *
     * @return bool Whether or not this entity type is a subtype of the given entity type.
     */
    public function isSubTypeOf(EntityType $entityType) : bool
    {
        if ($this === $entityType) {
            return true;
        }

        $parent = $this->getBaseType();

        while ($parent) {
            if ($parent === $entityType) {
                return true;
            }

            $parent = $parent->getBaseType();
        }

        return false;
    }
    
    /**
     * Returns true if this entity type can be instantiated, false if it cannot.
     * 
     * @return boolean Whether or not this entity type can be instantiated.
     */
    public function isAbstract() : bool
    {
        return $this->getReflection()->isAbstract();
    }
    
    /**
     * {@inheritDoc}
     */
    public function addStructuralPropertyDescription(StructuralPropertyDescription $propertyDescription)
    {
        parent::addStructuralPropertyDescription($propertyDescription);

        if ($propertyDescription instanceof PrimitivePropertyDescription) {
            if ($propertyDescription->isPartOfKey()) {
                $this->keyPropertyDescriptions[$propertyDescription->getName()] = $propertyDescription;
            }

            if ($propertyDescription->isPartOfETag()) {
                $this->eTagPropertyDescriptions[$propertyDescription->getName()] = $propertyDescription;
            }
        }
    }

    /**
     * Adds a navigation property description to the complex type.
     *
     * Adds a navigation property description to the complex type. No two properties on the same
     * complex type may have the same name.
     *
     * @param NavigationPropertyDescription $propertyDescription The property description to
     *                                                           be added to the complex type.
     *
     * @throws InvalidArgumentException Thrown if the complex type already has a property with
     *                                  the same name.
     */
    public function addNavigationPropertyDescription(NavigationPropertyDescription $propertyDescription)
    {
        if (isset($this->getPropertyDescriptions()[$propertyDescription->getName()])) {
            throw new InvalidArgumentException(sprintf(
                'Tried to add navigation property "%s" to entity type "%s", but this entity type already has a ' .
                'property with this name. The names of the properties defined on a structured type must be unique.',
                $propertyDescription->getName(),
                $this->getFullName()
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
     * Returns true if this entity type has one or more e-tag properties, false if it does not.
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
     * @return PrimitivePropertyDescription[] All key property descriptions for this entity type.
     */
    public function getKeyPropertyDescriptions() : array
    {
        if(isset($this->baseType)) {
            return array_merge($this->baseType->getKeyPropertyDescriptions(), $this->keyPropertyDescriptions);
        }

        return $this->keyPropertyDescriptions;
    }
    
    /**
     * Returns all E-tag property descriptions for this entity type.
     * 
     * @return PrimitivePropertyDescription[] All E-tag property descriptions for this entity type.
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
                parent::getPropertyDescriptions()
            );
        }

        return parent::getPropertyDescriptions();
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
