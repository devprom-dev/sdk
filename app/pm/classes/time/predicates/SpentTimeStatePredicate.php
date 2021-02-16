<?php

class SpentTimeStatePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    $rowsObject = $this->getObject()->getRowsObject();
 	    if ( $rowsObject->getEntityRefName() == 'pm_ChangeRequest' ) {
 	        $alias = 't';
        }
 	    else {
            $alias = 't2';
            $rowsObject = getFactory()->getObject('Task');
        }

 	    $predicate = new StateCommonPredicate($filter);
        $predicate->setObject($rowsObject);
        $predicate->setAlias($alias);

        return $predicate->getPredicate($filter);
 	}
}
