<?php

class AttachmentClassPredicate extends FilterPredicate
{
     function _predicate( $filter )
     {
         $classes = array_filter(preg_split('/,/', $filter), function($value) {
            return class_exists($value);
         });
         if ( count($classes) < 1 ) return " AND 1 = 2 ";

         $classes = array_map(function($value) {
             return strtolower($value);
         }, $classes);

         return " AND (
                    LCASE(t.ObjectClass) IN ('".join("','", $classes)."')
                    OR t.ObjectClass = 'comment'
                        AND EXISTS (SELECT 1 FROM Comment c
                                     WHERE LCASE(c.ObjectClass) IN ('".join("','", $classes)."')
                                       AND c.CommentId = t.ObjectId)
                )";
     }
}
