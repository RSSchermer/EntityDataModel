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

/**
 * An association describes a relation between two entity types.
 * 
 * @author Roland Schermer <roland0507@gmail.com>
 */
class Association
{
    const DELETE_ACTION_NONE = 0;
    
    const DELETE_ACTION_CASCADE = 1;
    
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var AssociationEnd
     */
    private $endOne;
    
    /**
     * @var AssociationEnd
     */
    private $endTwo;
    
    /**
     * @var integer
     */
    private $onDeleteAction;
    
    /**
     * @var EntityDataModel
     */
    private $entityDataModel;
    
    /**
     * Creates a new association.
     * 
     * @param string         $name           The name of the association (must contain 
     *                                       only alphanumber characters and underscores).
     * @param AssociationEnd $endOne         The first association end.
     * @param AssociationEnd $endTwo         The seconds association end.
     * @param integer        $onDeleteAction Can be 0 for no further actions when one
     *                                       of the ends is deleted, or 1 to cascade the
     *                                       deletion of one end to the other end.
     * 
     * @throws InvalidArgumentException Thrown if the association's name contains illegal characters.
     *                                  Thrown if a value other than 0 or 1 was supplied as the 
     *                                  "on delete" action.
     */
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
    
    /**
     * Sets the entity data model the association is a part of.
     * 
     * Sets the entity data model the association is a part of. An association should
     * always be part of some entity data model.
     * 
     * @param EntityDataModel $entityDataModel The entity data model the association is
     *                                         a part of.
     */
    public function setEntityDataModel(EntityDataModel $entityDataModel)
    {
        $this->entityDataModel = $entityDataModel;
    }
    
    /**
     * Returns the entity data model the association is a part of.
     * 
     * @return null|EntityDataModel The entity data model the association is a part of
     *                              or null if no entity data model is assigned yet.
     */
    public function getEntityDataModel()
    {
        return $this->entityDataModel;
    }
    
    /**
     * Returns the name of the association without namespace prefix.
     *
     * @return string The name of the association.
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Returns the full name of the association with namespace prefix.
     * 
     * Returns the name of the association with namespace prefix if an entity data
     * model was set or the name without a prefix if no entity data model was set.
     *
     * @return string The full name of the association.
     */
    public function getFullName()
    {
        return isset($this->entityDataModel) ? $this->entityDataModel->getNamespace() .'.'. $this->name : $this->name;
    }
    
    /**
     * Returns both association ends.
     * 
     * @return array Both association ends.
     */
    public function getEnds()
    {
        return array($this->endOne, $this->endTwo);
    }
    
    /**
     * Returns an association end based on its role name.
     * 
     * @param string $role The role name of the association end.
     * 
     * @return null|AssociationEnd The association end with the specified role or null if no
     *                             such role was found.
     */
    public function getEndByRole($role)
    {
        if ($this->endOne->getRole() === $role) {
            return $this->endOne;
        } elseif ($this->endTwo->getRole() === $role) {
            return $this->endTwo;
        }

        return null;
    }
    
    /**
     * Returns the "on delete" action.
     * 
     * Returns the action that, when one end is deleted, should be performed on the other end.
     * 0 for no action, 1 for cascading the delete.
     * 
     * @return integer Integer representing the "on delete" action: 0 indicates no delete action,
     *                 1 indicates cascading the delete action.
     */
    public function getOnDeleteAction()
    {
        return $this->onDeleteAction;
    }
}
