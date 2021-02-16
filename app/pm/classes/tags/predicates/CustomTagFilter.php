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

 		$clauses = array();
 		if ( strpos($filter, 'none') !== false ) {
            $clauses[] =
                " NOT EXISTS (SELECT 1 FROM pm_CustomTag rt " .
                "   		   WHERE rt.ObjectId = t." .$idfield.
                "				 AND rt.ObjectClass = '".$class."') ";
        }

        $tag = getFactory()->getObject('Tag');
        $tag_it = $tag->getExact( TextUtils::parseIds($filter) );

        if ( $tag_it->count() > 0 )
        {
            $clauses[] =
                " EXISTS (SELECT 1 FROM pm_CustomTag rt " .
                "   	   WHERE rt.ObjectId = t." .$idfield.
                "            AND rt.ObjectClass = '".$class."' ".
                "            AND rt.Tag IN (".join($tag_it->idsToArray(),',').")) ";
        }

		return count($clauses) < 1 ? " AND 1 = 2 " : " AND (".join(" OR ", $clauses).") ";
 	}
 	
 	function get( $filter )
 	{
 		$instance = new CustomTagFilter( $filter );
 		return $instance->getPredicate();
 	}
}