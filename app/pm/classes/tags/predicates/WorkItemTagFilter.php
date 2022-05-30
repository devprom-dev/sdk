<?php

class WorkItemTagFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
        $tag = getFactory()->getObject('Tag');
        $tag_it = $tag->getExact( TextUtils::parseIds($filter) );
        $idString = join(',',$tag_it->idsToArray());

 		$clauses = array(
            " EXISTS (SELECT 1 FROM pm_CustomTag rt 
                       WHERE rt.ObjectId = t.pm_TaskId 
                         AND rt.ObjectClass = 'task'
                         AND rt.Tag IN ({$idString})) ",

            " EXISTS (SELECT 1 FROM pm_RequestTag rt 
                       WHERE rt.Request = t.pm_TaskId
                         AND rt.Tag IN ({$idString})) "
        );


		return " AND (".join(" OR ", $clauses).") ";
 	}
}