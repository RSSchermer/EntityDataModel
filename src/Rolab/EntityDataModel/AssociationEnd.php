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

use Rolab\EntityDataModel\Type\EntityType;

/**
 * Describes one end of an association by its entity type, multiplicity and its role.
 * 
 * @author Roland Schermer <roland0507@gmail.com>
 */
class AssociationEnd
{
    const MULTIPLICITY_ZERO_OR_ONE = 0;

    const MULTIPLICITY_EXACTLY_ONE = 1;

    const MULTIPLICITY_MANY = 2;
    
    /**
     * @var string
     */
    private $role;
    
    /**
     * @var EntityType
     */
    private $entityType;
    
    /**
     * @var integer
     */
    private $multiplicity;
    
    /**
     * Creates a new association end.
     * 
     * @param string     $role         The role of the association end.
     * @param EntityType $entityType   The entity type represented by this association end
     * @param integer    $multiplicity An integer representing the multiplicity of the
     *                                 association end: 0 for zero or one, 1 for exactly one,
     *                                 2 for many
     * 
     * @throws InvalidArgumentException Thrown if a value other than 0, 1 or 2 is given for the 
     *                                  multiplicity
     */
    public function __construct($role, EntityType $entityType, $multiplicity = self::MULTIPLICITY_ZERO_OR_ONE)
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $role)) {
            throw new InvalidArgumentException(
                sprintf(
                    '"%s" is an illegal role for an association. The role for an association may only contain ' .
                    'alphanumeric characters and underscores.',
                    $role
                )
            );
        }
        
        $this->role = $role;
        $this->entityType = $entityType;

        if (!in_array(
            $multiplicity,
            array(self::MULTIPLICITY_ZERO_OR_ONE, self::MULTIPLICITY_EXACTLY_ONE, self::MULTIPLICITY_MANY)
        )) {
            throw new InvalidArgumentException(sprintf(
                '"%s" is an illegal value for multiplicity. Valid values are %s for zero or one, %s for exactly 1 ' .
                'and %s for many.',
                $multiplicity,
                self::MULTIPLICITY_ZERO_OR_ONE,
                self::MULTIPLICITY_EXACTLY_ONE,
                self::MULTIPLICITY_MANY
            ));
        }

        $this->multiplicity = $multiplicity;
    }
    
    /**
     * Returns the role of the association end.
     * 
     * @return string The role of the association end.
     */
    public function getRole()
    {
        return $this->role;
    }
    
    /**
     * Returns the entity type represented by the association end.
     * 
     * @return EntityType The entity type represented by the association end.
     */
    public function getEntityType()
    {
        return $this->entityType;
    }

    /**
     * Returns the multiplicity of the association end.
     * 
     * Returns the multiplicity of the association end as an integer:
     * 0 for zero or 1, 1 for exactly 1 and 2 for many.
     * 
     * @return integer The multiplicity of the association end.
     */
    public function getMultiplicity()
    {
        return $this->multiplicity;
    }
}
