<?php

class RequestTypePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array(
 			" IFNULL((SELECT i.ReferenceName FROM pm_IssueType i WHERE i.pm_IssueTypeId = t.Type),'z') TypeBase "
 		);
 	}

	function map( & $parms )
	{
		if ( $parms['TypeBase'] == '' ) return;
		if ( $parms['TypeBase'] == 'z' ) {
			$parms['Type'] = 'NULL';
		}
		else {
			$parms['Type'] = getFactory()->getObject('RequestType')->getRegistry()->Query(
					array (
							new FilterAttributePredicate('ReferenceName', $parms['TypeBase']),
							new FilterBaseVpdPredicate()
					)
			)->getId();
		}
		unset($parms['TypeBase']);
	}

	function IsPersisterImportant() {
        return true;
    }
}

