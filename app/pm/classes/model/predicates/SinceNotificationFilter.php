<?php

class SinceNotificationFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    if ( !$filter instanceof UserIterator ) return " AND 1 = 2 ";
 		return " AND EXISTS (
 		            SELECT 1 FROM ObjectChangeNotification ocn 
 		             WHERE ocn.SystemUser = ".$filter->getId()."
 		               AND ocn.ObjectId = t.".$this->getObject()->getIdAttribute()."
 		               AND LCASE(ocn.ObjectClass) = '".strtolower(get_class($this->getObject()))."'
 		               AND ocn.RecordCreated <= t.RecordCreated ) ";
 	}
}

 