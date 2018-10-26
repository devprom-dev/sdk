<?php

class WatcherUserPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
        $ids = \TextUtils::parseIds(
            preg_replace('/user-id/i', getSession()->getUserIt()->getId(), $filter)
        );
        if ( count($ids) < 1 ) return " AND 1 = 2 ";

 		$user_it = getFactory()->getObject('cms_User')->getRegistry()->Query(
            array (
                new FilterInPredicate($ids)
            )
 		);
 		if ( $user_it->count() < 1 ) return " AND 1 = 2 ";

        $classes = array(
            strtolower(get_class($this->getObject())),
            $this->getObject()->getEntityRefName()
        );
        if ( $this->getObject() instanceof WorkItem ) {
            $classes[] = 'request';
            $classes[] = 'pm_ChangeRequest';
        }

 		return " AND EXISTS (SELECT 1 FROM pm_Watcher w ".
 			   "			  WHERE w.ObjectClass IN ('".join("','",$classes)."') ".
 		       "				AND w.ObjectId = t.".$this->getObject()->getIdAttribute().
 			   "				AND w.SystemUser IN (".join(',', $user_it->idsToArray()).") ) ";
 	}
}
