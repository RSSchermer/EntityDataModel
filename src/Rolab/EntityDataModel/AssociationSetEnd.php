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

class AssociationSetEnd
{
    private $associationEnd;
    
    private $entitySet;
    
    public function __construct(AssociationEnd $associationEnd, EntitySet $entitySet)
    {
        $this->associationEnd = $associationEnd;
        $this->entitySet = $entitySet;
    }
    
    public function getAssociationEnd()
    {
        return $this->associationEnd;
    }
    
    public function getEntitySet()
    {
        return $this->entitySet;
    }
}
