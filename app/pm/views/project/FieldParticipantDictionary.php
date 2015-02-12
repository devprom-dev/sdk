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
 						new UserWorkerPredicate(),
 						new UserParticipatesDetailsPersister()
 				)
		);

 		$groups = array();
	    
	 	while ( !$part_it->end() ) 
 		{
		    $groups[$part_it->get('ProjectRole')][] = array (
		        'value' => $part_it->getId(),
                'caption' => $part_it->getDisplayName()
            );
 			
 			$part_it->moveNext();
 		}
 		
 		$options = array();
 		
 		foreach( $groups as $group => $items )
 		{
 			foreach( $items as $item )
 			{
 				$options[] = array_merge( $item, array('group' => $group) );
 			}
 		}

 		return $options;
	}
}