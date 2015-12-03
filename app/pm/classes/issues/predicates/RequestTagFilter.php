<?php

class RequestTagFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
		if ( in_array(trim($filter), array('0', 'none')) )
		{
			$predicate = " AND NOT EXISTS (SELECT 1 FROM pm_RequestTag rt " .
						 "   		    	WHERE rt.Request = t.pm_ChangeRequestId) ";
		}
		else
		{
			$tag = $model_factory->getObject('Tag');
			
			$tag_it = $tag->getExact( preg_split('/[,-]/', $filter) );
			
			if ( $tag_it->count() > 0 )
			{
				$predicate = " AND EXISTS (SELECT 1 FROM pm_RequestTag rt " .
							 "   		    WHERE rt.Request = t.pm_ChangeRequestId " .
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
 		$instance = new RequestTagFilter( $filter );
 		
 		return $instance->getPredicate();
 	}
}
