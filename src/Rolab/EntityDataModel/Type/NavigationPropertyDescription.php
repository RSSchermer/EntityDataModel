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

use Rolab\EntityDataModel\Type\ResourcePropertyDescription;
use Rolab\EntityDataModel\Association;

/**
 * Describes a navigation property of an entity type.
 * 
 * @author Roland Schermer <roland0507@gmail.com>
 */
class NavigationPropertyDescription extends ResourcePropertyDescription
{
    /**
     * @var Association
     */
    private $association;
    
    /**
     * @var string
     */
    private $toRole;
    
    /**
     * @var string
     */
    private $fromRole;
    
    /**
     * Creates a new navigation property description.
     * 
     * @param string             $name        The name of the navigation property description.
     * @param ReflectionProperty $reflection  Reflection of the property described.
     * @param Association        $association The association describing the navigation relation.
     * @param string             $fromRole    The name of the 'from' role in the association.
     * @param string             $toRole      The name of the 'to' role in the association.
     * 
     * @throws InvalidArgumentException Thrown if no role exists on the association with the 'from' role name.
     *                                  Thrown if no role exists on the association with the 'to' role name.
     */
    public function __construct($name, \ReflectionProperty $reflection, Association $association, $fromRole, $toRole)
    {
        parent::__construct($name, $reflection);

        $this->association = $association;

        if (is_null($association->getEndByRole($fromRole))) {
            throw new InvalidArgumentException(sprintf(
                'Role "%s" could not be found in the association. The "from" role must reference a role name of ' .
                'an association end in the association.',
                $fromRole
            ));
        }

        $this->fromRole = $fromRole;

        if (is_null($association->getEndByRole($toRole))) {
            throw new InvalidArgumentException(sprintf(
                'Role "%s" could not be found in the association. The "to" role must reference a role name of an ' .
                'association end in the association.',
                $toRole
            ));
        }

        $this->toRole = $toRole;
    }
    
    /**
     * Returns the association that describes the navigation relation.
     * 
     * @return Association The association that describes the navigation relation.
     */
    public function getAssociation()
    {
        return $this->association;
    }
    
    /**
     * Returns the name of the 'from' role.
     * 
     * @return string The name of the 'from' role.
     */
    public function getFromRole()
    {
        return $this->fromRole;
    }
    
    /**
     * Returns the name of the 'to' role.
     * 
     * @return string The name of the 'to' role.
     */
    public function getToRole()
    {
        return $this->toRole;
    }
}
