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
use Rolab\EntityDataModel\EntitySet;

/**
 * Links one end of an association to an entity set.
 * 
 * @author Roland Schermer <roland0507@gmail.com>
 */
class AssociationSetEnd
{
    /**
     * @var AssociationEnd
     */
    private $associationEnd;
    
    /**
     * @var EntitySet
     */
    private $entitySet;
    
    /**
     * Creates a new association set end.
     * 
     * @param AssociationEnd $associationEnd The association end represented by the association 
     *                                       end.
     * @param EntitySet      $entitySet      The entity set linked to the association end.
     */
    public function __construct(AssociationEnd $associationEnd, EntitySet $entitySet)
    {
        $this->associationEnd = $associationEnd;
        $this->entitySet = $entitySet;
    }
    
    /**
     * Returns the association end represented by the association set end.
     * 
     * @return AssociationEnd The association end represented by the association set end.
     */
    public function getAssociationEnd()
    {
        return $this->associationEnd;
    }

    /**
     * Returns the entity set that is linked to the association end by this
     * association set end.
     * 
     * @return EntitySet The entity set that is link to an association end by
     *                   this association set end.
     */
    public function getEntitySet()
    {
        return $this->entitySet;
    }
}
