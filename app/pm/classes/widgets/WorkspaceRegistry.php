<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectRegistrySQL.php";

class WorkspaceRegistry extends ObjectRegistrySQL
{
	public function getDefault( $predicates = array() )
	{
		$it = $this->Query(
 	    		array_merge($predicates, array (
 	    				new \FilterAttributePredicate('SystemUser', 
 	    						array(
 	    								getSession()->getUserIt()->getId(), // search for user's settings 
 	    								'none' // search for common settings
 	    						)
 	    				),
 	    				new \FilterBaseVpdPredicate(),
 	    				new \SortAttributeClause('SystemUser.D') // user settings overrides common settings
 	    		))
		);
		
		$ids = array();
		
		while( !$it->end() )
		{
			if ( !array_key_exists($it->get('UID'), $ids) )
			{
				$ids[$it->get('UID')] = $it->getId();
			}
			
			$it->moveNext();
		}
		
		if ( count($ids) < 1 ) return $this->getObject()->getEmptyIterator();
		 
		return $this->Query(
				array (
						new FilterInPredicate(array_values($ids))
				)
		);
	}
}