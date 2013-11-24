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

use Rolab\EntityDataModel\Association;
use Rolab\EntityDataModel\AssociationSetEnd;
use Rolab\EntityDataModel\EntityContainer;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;

class AssociationSet
{
    private $name;

    private $association;

    private $setEndOne;

    private $setEndTwo;

    private $entityContainer;

    public function __construct($name, Association $association, AssociationSetEnd $setEndOne = null,
        AssociationSetEnd $setEndTwo = null
    ) {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) {
            throw new InvalidArgumentException(sprintf('"%s" is an illegal name for an association set. The name for ' .
                'an association set may only contain alphanumeric characters and underscores.', $name));
        }

        $this->name = $name;
        $this->association = $association;
        $this->setEndOne = $setEndOne;
        $this->setEndTwo = $setEndTwo;
    }

    public function setEntityContainer(EntityContainer $entityContainer)
    {
        $this->entityContainer = $entityContainer;
    }

    public function getEntityContainer()
    {
        return $this->entityContainer;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAssociation()
    {
        return $this->association;
    }

    public function getSetEnds()
    {
        $setEnds = array();

        if (null !== $this->setEndOne) {
            $setEnds[] = $this->setEndOne;
        }

        if (null !== $this->setEndTwo) {
            $setEnds[] = $this->setEndTwo;
        }

        return $setEnds;
    }

    public function getSetEndByRole($role)
    {
        if (null !== $this->setEndOne && $this->setEndOne->getAssociationEnd()->getRole() === $role) {
            return $this->setEndOne;
        } elseif (null !== $this->setEndTwo && $this->setEndTwo->getAssociationEnd()->getRole() === $role) {
            return $this->setEndTwo;
        }

        return null;
    }
}
