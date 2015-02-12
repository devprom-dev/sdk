<?php

class WatcherUserPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$user_it = getFactory()->getObject('cms_User')->getRegistry()->Query(
 				array (
 						new FilterInPredicate(preg_split('/,/', $filter))
 				)
 		);
 		
 		if ( $user_it->count() < 1 ) return " AND 1 = 2 ";

 		return " AND EXISTS (SELECT 1 FROM pm_Watcher w ".
 			   "			  WHERE w.ObjectClass = '".strtolower(get_class($this->getObject()))."' ".
 		       "				AND w.ObjectId = t.".$this->getObject()->getIdAttribute().
 			   "				AND w.SystemUser IN (".join(',', $user_it->idsToArray()).") ) ";
 	}
}
