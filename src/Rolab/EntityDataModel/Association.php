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

use Rolab\EntityDataModel\AssociationEnd;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;

class Association
{
    const DELETE_ACTION_NONE = 0;

    const DELETE_ACTION_CASCADE = 1;

    private $name;

    private $endOne;

    private $endTwo;

    private $onDeleteAction;

    private $entityDataModel;

    public function __construct($name, AssociationEnd $endOne, AssociationEnd $endTwo,
        $onDeleteAction = self::DELETE_ACTION_NONE
    ) {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) {
            throw new InvalidArgumentException(sprintf('"%s" is an illegal name for an association. The name for ' .
                'an association may only contain alphanumeric characters and underscores.', $name));
        }

        $this->name = $name;
        $this->endOne = $endOne;
        $this->endTwo = $endTwo;

        if ($onDeleteAction !== self::DELETE_ACTION_NONE && $onDeleteAction !== self::DELETE_ACTION_CASCADE) {
            throw new InvalidArgumentException(sprintf(
                '"%s" is an illegal value for the "on delete" action.',
                $onDeleteAction
            ));
        }

        $this->onDeleteAction = $onDeleteAction;
    }

    public function setEntityDataModel(EntityDataModel $entityDataModel)
    {
        $this->entityDataModel = $entityDataModel;
    }

    public function getEntityDataModel()
    {
        return $this->entityDataModel;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getFullName()
    {
        return isset($this->entityDataModel) ? $this->entityDataModel->getNamespace() .'.'. $this->name : $this->name;
    }

    public function getEnds()
    {
        return array($this->endOne, $this->endTwo);
    }

    public function getEndByRole($role)
    {
        if ($this->endOne->getRole() === $role) {
            return $this->endOne;
        } elseif ($this->endTwo->getRole() === $role) {
            return $this->endTwo;
        }

        return null;
    }

    public function getOnDeleteAction()
    {
        return $this->onDeleteAction;
    }
}
