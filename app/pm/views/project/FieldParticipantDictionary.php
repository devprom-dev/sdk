<?php

class FieldParticipantDictionary extends FieldDictionary
{
	function getGroups()
	{
		$groups = array();
		
		$role_it = getFactory()->getObject('ProjectRole')->getRegistry()->Query(
				array (
						new FilterBaseVpdPredicate()
				)
		);
		
		while( !$role_it->end() )
		{
			$groups[$role_it->getId()] = array (
					'label' => $role_it->getDisplayName()
			);
			$role_it->moveNext();
		}
			
		return $groups;
	}
	
 	function getOptions()
	{
 		$part_it = $this->getObject()->getRegistry()->Query(
 				array (
 						new FilterBaseVpdPredicate(),
 						new SortAttributeClause('ProjectRole'),
 						new FilterAttributePredicate('IsActive', 'Y')
 				)
		);

 		$options = array();
	    
	 	while ( !$part_it->end() ) 
 		{
		    $options[] = array (
		        'value' => $part_it->getId(),
                'caption' => $part_it->getDisplayName(),
                'group' => $part_it->get('ProjectRole')
            );
 			
 			$part_it->moveNext();
 		}

		return $options;
	}
}