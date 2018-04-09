<?php

class ChangeLogSinceNotificationFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    if ( !$filter instanceof UserIterator ) return " AND 1 = 2 ";
 		return " AND EXISTS (
 		            SELECT 1 FROM ObjectChangeNotification ocn 
 		             WHERE ocn.SystemUser = ".$filter->getId()."
 		               AND ocn.ObjectId = t.ObjectId
 		               AND LCASE(ocn.ObjectClass) = t.ClassName
 		               AND ocn.RecordCreated <= t.RecordCreated ) ";
 	}
}

 