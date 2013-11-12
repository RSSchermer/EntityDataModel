<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\EntityDataModel\Type\PropertyDescription;

use Rolab\EntityDataModel\Type\PropertyDescription\ResourcePropertyDescription;
use Rolab\EntityDataModel\Association;

class NavigationPropertyDescription extends ResourcePropertyDescription
{
    private $association;
    
    private $toRole;
    
    private $fromRole;

    public function __construct($name, \ReflectionProperty $reflection, Association $association, $toRole, $fromRole)
    {
        parent::__construct($name, $reflection);

        $this->association = $association;
        
        if (is_null($association->getEndByRole($toRole))) {
            throw new InvalidArgumentException(sprintf('To role "%s" could not be found in the association. The "to" role must' .
                'reference a role name of an association end in the association.', $toRole));
        }
        
        $this->toRole = $toRole;
        
        if (is_null($association->getEndByRole($fromRole))) {
            throw new InvalidArgumentException(sprintf('Role "%s" could not be found in the association. The "from" role must' .
                'reference a role name of an association end in the association.', $fromRole));
        }
        
        $this->fromRole = $fromRole;
    }

    public function getAssocation()
    {
        return $this->targetEntitySet;
    }
    
    public function getToRole()
    {
        return $this->fromRole;
    }
    
    public function getFromRole()
    {
        return $this->fromRole;
    }
}
