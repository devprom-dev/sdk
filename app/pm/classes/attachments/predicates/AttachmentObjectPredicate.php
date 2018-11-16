<?php

class AttachmentObjectPredicate extends FilterPredicate
{
     function _predicate( $filter )
     {
         if ( !is_a($filter, 'IteratorBase') ) {
             return " AND t.ObjectId = -1 ";
         }
         if ( $filter->count() < 1 ) {
             return " AND t.ObjectId = -1 ";
         }

         $classes = array(
             strtolower($filter->object->getClassName()),
             strtolower(get_class($filter->object))
         );

         if ( $filter->object instanceof Request ) {
             $classes[] = 'issue';
         }
         if ( $filter->object instanceof Issue ) {
             $classes[] = 'request';
         }

         return " AND t.ObjectId IN (".join(',',$filter->idsToArray()).") ".
                " AND LCASE(t.ObjectClass) IN ('".join("','", $classes)."')";
     }
}
