<?php

class CustomTagFilter extends FilterPredicate
{
 	var $object;
 	
 	function CustomTagFilter( $object, $filter )
 	{
 		$this->object = $object;
 		parent::FilterPredicate( $filter );
 	}
 	
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		$class = strtolower(get_class($this->object));
 		$idfield = $this->object->getClassName().'Id';

		if ( trim($filter) == 'none' )
		{
			$predicate = " AND NOT EXISTS (SELECT 1 FROM pm_CustomTag rt " .
						 "   		    	WHERE rt.ObjectId = t." .$idfield.
						 "					  AND rt.ObjectClass = '".$class."') ";
		}
		else
		{
			$tag = $model_factory->getObject('Tag');
			$tag_it = $tag->getExact( preg_split('/[,-]/', $filter) );
			
			if ( $tag_it->count() > 0 )
			{
				$predicate = " AND EXISTS (SELECT 1 FROM pm_CustomTag rt " .
							 "   		    WHERE rt.ObjectId = t." .$idfield.
							 "                AND rt.ObjectClass = '".$class."' ".
							 "                AND rt.Tag IN (".join($tag_it->idsToArray(),',').")) ";
			}
			else
			{
				$predicate = '';
			}
		}
		
		return $predicate;
 	}
 	
 	function get( $filter )
 	{
 		$instance = new CustomTagFilter( $filter );
 		return $instance->getPredicate();
 	}
}