<?php

class CustomTagFilter extends FilterPredicate
{
 	var $object;
 	
 	function CustomTagFilter( $object, $filter )
 	{
 		$this->object = $object;
 		parent::__construct( $filter );
 	}
 	
 	function _predicate( $filter )
 	{
 		$class = strtolower(get_class($this->object));
 		$idfield = $this->object->getClassName().'Id';
 		$sqls = array();

        if ( in_array('none', TextUtils::parseItems($filter)) ) {
            $sqls[] = " NOT EXISTS (
                            SELECT 1 FROM pm_CustomTag rt 
                             WHERE rt.ObjectId = t.{$idfield} 
                               AND rt.ObjectClass = '{$class}') ";
        }

        if ( in_array('any', TextUtils::parseItems($filter)) ) {
            $sqls[] = " EXISTS (
                            SELECT 1 FROM pm_CustomTag rt 
                             WHERE rt.ObjectId = t.{$idfield} 
                               AND rt.ObjectClass = '{$class}') ";
        }

        $tag = getFactory()->getObject('Tag');
        $tag_it = $tag->getExact( TextUtils::parseIds($filter) );

        if ( $tag_it->count() > 0 ) {
            $sqls[] =
                " EXISTS (
                    SELECT 1 FROM pm_CustomTag rt 
                     WHERE rt.ObjectId = t.{$idfield}
                       AND rt.ObjectClass = '{$class}' 
                       AND rt.Tag IN (".join($tag_it->idsToArray(),',').")) ";
        }

        if ( count($sqls) < 1 ) return " AND 1 = 2 ";

		return " AND (" . join(" OR ", $sqls) . ") ";
 	}
 	
 	function get( $filter )
 	{
 		$instance = new CustomTagFilter( $filter );
 		return $instance->getPredicate();
 	}
}