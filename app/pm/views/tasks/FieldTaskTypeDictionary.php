<?php

include_once SERVER_ROOT_PATH."pm/classes/tasks/persisters/TaskTypeBaseDetailsPersister.php";

class FieldTaskTypeDictionary extends FieldDictionary
{
	function getGroups()
	{
		$groups = array();
		
	 	$type_it = getFactory()->getObject('TaskTypeBase')->getRegistry()->Query(
	 		array(
	 			new TaskTypeBaseDetailsPersister(),
	 			new SortOrderedClause()
	 		)
	 	);
	 	
	 	while( !$type_it->end() )
	 	{
	 		if ( $type_it->get('SubTypesCount') > 1 )
	 		{
		 		$groups[$type_it->getId()] = array (
		 				'label' => $type_it->getDisplayName()
		 		);
	 		}
	 		$type_it->moveNext();
	 	}
	 	
	 	return $groups;
	}
	
 	function getOptions()
	{
	    $options = array();
	    
	 	$type_it = $this->getObject()->getRegistry()->Query(
	 		array(
	 			new FilterBaseVpdPredicate(),
	 			new SortAttributeClause('ParentTaskType')
	 		)
	 	);
	 	
		while( !$type_it->end() )
		{
		    
		    $options[] = array (
                'value' => $type_it->getId(),
                'referenceName' => $type_it->get('ReferenceName'),
                'caption' => $type_it->getDisplayName(),
		    	'group' => $type_it->get('ParentTaskType')
            );

		    $type_it->moveNext();
		}
		
		return $options;
	}
}