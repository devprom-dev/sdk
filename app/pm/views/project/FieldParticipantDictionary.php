<?php

class FieldParticipantDictionary extends FieldDictionary
{
 	function getOptions()
	{
 		$part_it = $this->getObject()->getRegistry()->Query(
 				array (
 						new FilterBaseVpdPredicate(),
 						new SortAttributeClause('ProjectRole'),
 						new FilterAttributePredicate('IsActive', 'Y')
 				)
		);

 		$roles = array();
 		$options = array();
	    
	 	while ( !$part_it->end() ) 
 		{
 			if ( !in_array($part_it->get('ProjectRole'), $roles) )
 			{
    		    $options[] = array (
                    'caption' => join(', ', $part_it->getRef('ProjectRole')->fieldToArray('Caption')),
                    'disabled' => true
                );
    		    
    		    $roles[] = $part_it->get('ProjectRole');
 			}

		    $options[] = array (
		        'value' => $part_it->getId(),
                'caption' => $part_it->getDisplayName(),
                'disabled' => false
            );
 			
 			$part_it->moveNext();
 		}

		return $options;
	}
}