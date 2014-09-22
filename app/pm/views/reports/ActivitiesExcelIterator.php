<?php

include_once SERVER_ROOT_PATH.'core/classes/export/IteratorExportExcel.php';
 
class ActivitiesExcelIterator extends IteratorExportExcel
{
 	var $group_participants, $participant_tasks;
 	
 	private $task_it;
 	
 	private $request_it;
 	
 	function ActivitiesExcelIterator( $iterator )
 	{
 		$this->group_participants = false;
 		$this->participant_tasks = array();
 		
 		$ids = array();
 		
 		while ( !$iterator->end() )
 		{
 			if ( $iterator->get('Item') == 'Task' || $iterator->get('Item') == 'ChangeRequest' )
 			{
 				$this->group_participants = true;
				$this->participant_tasks[$iterator->get('SystemUser')] += 1;
 			}

 			if ( $iterator->get('ItemId') > 0 )
 			{
 				$ids[] = $iterator->get('ItemId');
 			}
 			
 			$iterator->moveNext();
 		}
 		
 		$iterator->moveFirst();
 		
 		$this->task_it = getFactory()->getObject('Task')->getRegistry()->Query(
	 				array (
	 						new FilterInPredicate($ids)
	 				)
 			);
 		
 		$this->request_it = getFactory()->getObject('Request')->getRegistry()->Query(
	 				array (
	 						new FilterInPredicate($ids)
	 				)
 			);
 		
 		parent::IteratorExportExcel( $iterator );
 	}
 	
 	function get( $field )
 	{
 		global $model_factory;
 		
 		$iterator = $this->getIterator();
 		$activities_map = $iterator->getActivitiesMap();

 		switch ( $field )
 		{
 			case 'ItemId':
 				switch ( $iterator->get('Item') )
 				{
 					case 'Project':
						$project = $model_factory->getObject('pm_Project');
						$project_it = $project->getExact($iterator->get('ItemId')); 
 						return $project_it->getDisplayName();
 				    
 				    case 'Participant':
						$part = $model_factory->getObject('cms_User');
						$part_it = $part->getExact($iterator->get('ItemId')); 
 						return $part_it->getDisplayName();
 						
 					case 'Task':
 						$this->task_it->moveToId($iterator->get('ItemId'));

 						return '[T-'.$this->task_it->getId().'] '.$this->task_it->getDisplayName();

 					case 'ChangeRequest':
						
						if ( $iterator->get('ItemId') < 1 )
						{
	 						return text(756);
						}
						else
						{
 							$this->request_it->moveToId($iterator->get('ItemId'));
 							
	 						return '[I-'.$this->request_it->getId().'] '.$this->request_it->getDisplayName();
						}
 				}
 			
 			case 'Total':
 			    
 			    foreach( preg_split('/,/', $iterator->get('SystemUser')) as $user_id )
 			    {
 			        $data = $activities_map[$iterator->get('Item').$iterator->get('ItemId')][$user_id];
 			        
 			        $total += is_array($data) ? array_sum($data) : 0;
 			    }
 			    
 				return $total == 0 ? '' : str_replace(',', '.', $total);
 				
 			default:
 			    
 			    foreach( preg_split('/,/', $iterator->get('SystemUser')) as $user_id )
 			    {
 			        $total += $activities_map[$iterator->get('Item').$iterator->get('ItemId')][$user_id][substr($field, 1) + 1];
 			    }
 			    
 				return $total == 0 ? '' : str_replace(',', '.', $total);
 		}
 	}
 	
 	function getFormula( $row, $cell )
 	{
 		$fields = $this->getFields();
 	
 		if ( $cell == count($fields) - 1 )
 		{
 			return "SUM(RC[-".(count($fields) - 2)."]:RC[-1])";
 		}
 		else if ( $this->group_participants && $cell > 0 )
 		{
 			$iterator = $this->getIterator();
 			
 			if ( in_array($iterator->get('Item'), array('Participant', 'Project')) )
 			{
	 			return "SUM(R[1]C:R[".($this->participant_tasks[$iterator->get('ItemId')])."]C)";
 			}
 		}
 	}

 	function getFields()
 	{
 	    global $model_factory;
 	    
 		$iterator = $this->getIterator();
 		
 		$days_map = $iterator->getDaysMap();
 		
 		if ( count($days_map) > 12 )
 		{
     		$fields = array ( 'ItemId' => $iterator->object->getAttributeUserName('Caption') );
     			
     		for ( $i = 0; $i < count($days_map); $i++ )
     		{
     			$fields['D'.$i] = $days_map[$i];
     		}
 		}
 		elseif ( count($days_map) == 12 )
 		{
     		$fields = array ( 'ItemId' => text(1298) );
     			
     		$date = $model_factory->getObject('DateMonth');
     		
     		$date_it = $date->getAll();
     		
     		while( !$date_it->end() )
     		{
     			$fields['D'.($date_it->getId() - 1)] = $date_it->getDisplayName();
     			
     		    $date_it->moveNext();
     		}
 		}
 		else
 		{
     		$fields = array ( 'ItemId' => text(1299) );
     			
     		for ( $i = 0; $i < count($days_map); $i++ )
     		{
     			$fields['D'.$i] = $days_map[$i];
     		}
 		}
 		
		$fields = array_merge($fields, 
			array( 'Total' => translate('Итого')) );

 		return $fields;
 	}
 	
	function getWidth( $field )
	{
 		switch ( $field )
 		{
 			case 'ItemId':
 				return $this->group_participants ? 250 : 150;

 			case 'Total':
 				return 60;

 			default:
 				return 20;
 		}
	}

 	function getRowStyle( $object_it )
 	{
 		$iterator = $this->getIterator();
 		
 		if ( in_array($iterator->get('Item'), array('Participant', 'Project')) && $this->group_participants )
 		{
 			return 's22';
 		}
 		
 		return '';
 	}
}
