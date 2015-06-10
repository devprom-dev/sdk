<?php

include_once SERVER_ROOT_PATH.'core/classes/export/IteratorExportExcel.php';
 
class ActivitiesExcelIterator extends IteratorExportExcel
{
 	private $row_it;
 	
 	function ActivitiesExcelIterator( $iterator )
 	{
 		$ids = $iterator->fieldToArray('ItemId');
 		
 		$this->row_it = $this->getRowsObject()->getRegistry()->Query(
	 				array (
	 						new FilterInPredicate($ids)
	 				)
 			);
 		$this->group_it = $this->getGroupObject()->getAll();
 		
 		$iterator->moveFirst();
 		parent::IteratorExportExcel( $iterator );
 	}

	function getRowsObject()
	{
		if ( is_object($this->rows_object) ) return $this->rows_object;
		switch( $_REQUEST['view'] )
		{
			case 'issues':
				return getFactory()->getObject('Request');
			case 'participants':
				return getFactory()->getObject('User');
			case 'projects':
				return getFactory()->getObject('Project');
			case 'tasks':
				return getFactory()->getObject('Task');
			default:
				return getFactory()->getObject('Request');
		}
	}
	
	function getGroupObject()
	{
		if ( $_REQUEST['group'] == '' ) return getFactory()->getObject('User');
		if ( !$this->getRowsObject()->IsReference($_REQUEST['group']) ) {
			switch($_REQUEST['group']) {
				case 'Project':
					return getFactory()->getObject('Project');
				default:
					return getFactory()->getObject('User');
			}
		}
		return $this->getRowsObject()->getAttributeObject($_REQUEST['group']);
	}
 	
 	function get( $field )
 	{
 		$iterator = $this->getIterator();
 		$uid = new ObjectUID;

 		switch ( $field )
 		{
 			case 'ItemId':
 				if ( $iterator->get('Group') > 0 ) {
 						$this->group_it->moveToId($iterator->get('ItemId'));
 						return $this->group_it->getDisplayName();
 				} 
 				else {
 						$this->row_it->moveToId($iterator->get('ItemId'));
 						if ( !$uid->hasUid($this->row_it) ) {
 							return $this->row_it->getDisplayName();
 						}
 						else {
	 						$info = $uid->getUidInfo($this->row_it);
	 						$result = '['.$info['uid'].'] '.$info['caption'];
	 						if ( $info['state_name'] != '' ) $result .= ' ('.$info['state_name'].')';
	 						return $result;
 						}
 				}
 				break;
 			
 			case 'Total':
 				return $iterator->get('Total') == 0 ? '' : str_replace(',', '.', $iterator->get('Total'));
 				
 			default:
 				return $iterator->get($field) == 0 ? '' : str_replace(',', '.', $iterator->get($field));
 		}
 	}
 	
 	function getFormula( $row, $cell )
 	{
 		$fields = $this->getFields();
 	
 		if ( $cell == count($fields) - 1 )
 		{
 			return "SUM(RC[-".(count($fields) - 2)."]:RC[-1])";
 		}
 		else if ( $cell > 0 )
 		{
 			$iterator = $this->getIterator();
 			if ( $iterator->get('Group') > 0 )
 			{
	 			return "SUM(R[1]C:R[".($iterator->get('Total'))."]C)";
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
     			$fields['Day'.($i+1)] = $days_map[$i];
     		}
 		}
 		elseif ( count($days_map) == 12 )
 		{
     		$fields = array ( 'ItemId' => text(1298) );
     			
     		$date = $model_factory->getObject('DateMonth');
     		
     		$date_it = $date->getAll();
     		
     		while( !$date_it->end() )
     		{
     			$fields['Day'.($date_it->getId() - 1)] = $date_it->getDisplayName();
     			
     		    $date_it->moveNext();
     		}
 		}
 		else
 		{
     		$fields = array ( 'ItemId' => text(1299) );
     			
     		for ( $i = 0; $i < count($days_map); $i++ )
     		{
     			$fields['Day'.($i+1)] = $days_map[$i];
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
 				return $this->getIterator()->get('Group') < 1 ? 250 : 150;

 			case 'Total':
 				return 60;

 			default:
 				return 20;
 		}
	}

 	function getRowStyle( $object_it )
 	{
 		if ( $this->getIterator()->get('Group') > 0 )
 		{
 			return 's22';
 		}
 		
 		return '';
 	}
}
