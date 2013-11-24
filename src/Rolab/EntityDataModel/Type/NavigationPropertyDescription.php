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

class NavigationPropertyDescription extends ResourcePropertyDescription
{
    private $association;

    private $toRole;

    private $fromRole;

    public function __construct($name, \ReflectionProperty $reflection, Association $association, $fromRole, $toRole)
    {
        parent::__construct($name, $reflection);

        $this->association = $association;

        if (is_null($association->getEndByRole($fromRole))) {
            throw new InvalidArgumentException(sprintf('Role "%s" could not be found in the association. The "from" role must' .
                'reference a role name of an association end in the association.', $fromRole));
        }

        $this->fromRole = $fromRole;

        if (is_null($association->getEndByRole($toRole))) {
            throw new InvalidArgumentException(sprintf('Role "%s" could not be found in the association. The "to" role must' .
                'reference a role name of an association end in the association.', $toRole));
        }

        $this->toRole = $toRole;
    }

    public function getAssocation()
    {
        return $this->association;
    }

    public function getFromRole()
    {
        return $this->fromRole;
    }

    public function getToRole()
    {
        return $this->toRole;
    }
}
