<?php

class FieldTaskTypeDictionary extends FieldDictionary
{
 	function getOptions()
	{
	    $options = array();
	    
        $task_type = $this->getObject();
		
	 	$type_it = $task_type->getRegistry()->Query(
	 		array(
	 			new FilterBaseVpdPredicate(),
	 			new SortAttributeClause('ParentTaskType')
	 		)
	 	);
	 	
	 	$new_group = false;
	 	
	 	$agg_it = $task_type->getSuperTypesAggIt();

	 	$parent_type = $type_it->get('ParentTaskType');
	 	
		while( !$type_it->end() )
		{
 			if ( $parent_type != $type_it->get('ParentTaskType') )
 			{
		 		$parent_type = $type_it->get('ParentTaskType');
 				
		 		$agg_it->moveTo('ParentTaskType', $type_it->get('ParentTaskType'));
 				
 				if ( $agg_it->get('SubItems') > 1 )
 				{
	 				$parent_type_it = $type_it->getRef('ParentTaskType');
	 				
        		    $options[] = array (
                        'caption' => $parent_type_it->getDisplayName(),
        		        'disabled' => true
                    );
		 			
		 			$new_group = true;
 				}
 			}
 			else
 			{
 				$new_group = false;
 			}
		    
		    $options[] = array (
                'value' => $type_it->getId(),
                'referenceName' => $type_it->get('ReferenceName'),
                'caption' => $type_it->getDisplayName()
            );

		    $type_it->moveNext();
		}
		
		return $options;
	}
}