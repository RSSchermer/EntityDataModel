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

class AssociationEnd
{
    const MULTIPLICITY_ZERO_OR_ONE = 0;

    const MULTIPLICITY_EXACTLY_ONE = 1;

    const MULTIPLICITY_MANY = 2;

    private $role;

    private $entityType;

    private $multiplicity;

    public function __construct($role, EntityType $entityType, $multiplicity = self::MULTIPLICITY_ZERO_OR_ONE)
    {
        $this->role = $role;
        $this->entityType = $entityType;

        if (!in_array($multiplicity,
            array(self::MULTIPLICITY_ZERO_OR_ONE, self::MULTIPLICITY_EXACTLY_ONE, self::MULTIPLICITY_MANY)
        )) {
            throw new InvalidArgumentException(sprintf('"%s" is an illegal value for multiplicity.',
                $multiplicity));
        }

        $this->multiplicity = $multiplicity;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function getEntityType()
    {
        return $this->entityType;
    }

    public function getMultiplicity()
    {
        return $this->multiplicity;
    }
}
