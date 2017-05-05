<?php

class ChangeLogStateFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
        $values = array_intersect(
            preg_split('/,/', $filter),
            getFactory()->getObject('StateCommon')->getAll()->fieldToArray('entityId')
        );
        if ( count($values) < 1 ) return " AND 1 = 2 ";

        $conditions = array(
            " (SELECT MAX(so.pm_StateObjectId)
                 FROM pm_StateObject so WHERE so.ObjectId = t.ObjectId AND so.ObjectClass = t.ClassName)
                 IN (SELECT so.pm_StateObjectId 
                       FROM pm_StateObject so, pm_State st 
                      WHERE so.ObjectId = t.ObjectId 
                        AND so.ObjectClass = t.ClassName
                        AND so.State = st.pm_StateId
                        AND st.IsTerminal IN ('".join("','", $values)."')) "
        );
        if ( in_array(StateCommonRegistry::Submitted, $values) ) {
            $conditions[] =
                " NOT EXISTS (SELECT 1 FROM pm_StateObject so
                               WHERE so.ObjectId = t.ObjectId
                                 AND so.ObjectClass = t.ClassName) ";
        }
        return " AND (".join(' OR ', $conditions).")";
    }
}