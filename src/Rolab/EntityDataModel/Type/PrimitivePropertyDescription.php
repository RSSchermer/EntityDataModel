<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Type;

use Rolab\EntityDataModel\Exception\InvalidArgumentException;

/**
 * Describes a primitive property on a structured type.
 *
 * A primitive property's value type is a primitive type.
 * 
 * @author Roland Schermer <roland0507@gmail.com>
 */
class PrimitivePropertyDescription extends StructuralPropertyDescription
{
    /**
     * @var bool
     */
    private $partOfKey;

    /**
     * @var bool
     */
    private $partOfETag;

    /**
     * Creates a new primitive property description.
     * 
     * @param string              $name              The name of the primitive property description. (may
     *                                               only consist of alphanumeric characters and the
     *                                               underscore).
     * @param \ReflectionProperty $reflection        A reflection object for the property being described.
     * @param PrimitiveType       $propertyType      The type of the property value.
     * @param bool                $isCollection      Whether or not the property value is a collection.
     * @param bool                $nullable          Whether or not the property value can be null.
     * @param bool                $partOfKey         Whether or not the property is part of the key of an
     *                                               entity type.
     * @param bool                $partOfETag        Whether not the property is part of the E-tag of an
     *                                               entity type.
     * 
     * @throws InvalidArgumentException Thrown if the name contains illegal characters.
     */
    public function __construct(
        string $name,
        \ReflectionProperty $reflection,
        PrimitiveType $propertyType,
        bool $isCollection = false,
        bool $nullable = true,
        bool $partOfKey = false,
        bool $partOfETag = false
    ) {
        parent::__construct($name, $reflection, $propertyType, $isCollection, $nullable);

        $this->partOfKey = $partOfKey;
        $this->partOfETag = $partOfETag;
    }

    /**
     * Returns true if this property is part of its owner entity type's key, false if it is not.
     *
     * @return bool Whether or not this property is part of an entity type's key.
     */
    public function isPartOfKey() : bool
    {
        return $this->partOfKey;
    }

    /**
     * Returns true if this property is part of its owner entity type's E-tag, false if it is not.
     *
     * @return bool Whether or not this property is part of an entity type's E-tag.
     */
    public function isPartOfETag() : bool
    {
        return $this->partOfETag;
    }
}
