<?php

class RequestDetailsPersister extends ObjectSQLPersister
{
	function map( &$parms )
	{
		if ( $parms['Caption'] != '' ) {
			$parms['Caption'] = TextUtils::stripAnyTags($parms['Caption']);
		}
	}

 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = "(SELECT tp.Caption FROM pm_IssueType tp WHERE tp.pm_IssueTypeId = t.Type) TypeName ";
        $columns[] = "(SELECT tp.ReferenceName FROM pm_IssueType tp WHERE tp.pm_IssueTypeId = t.Type) TypeReferenceName ";

 		return $columns;
 	}

    function IsPersisterImportant() {
        return true;
    }
}
