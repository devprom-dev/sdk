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
 						    $html = $uid->getUidWithCaption($this->row_it);
                            $html = preg_replace('/<i[^<]+<\/i>/i', '', $html);
	 						$html = new \Html2Text\Html2Text($html, array('do_links' => 'none'));
	 						return $html->getText();
 						}
 				}
 				break;
 			
 			case 'Total':
 				return $iterator->get('Total') == 0 ? '' : str_replace(',', '.', $iterator->get('Total'));
 				
 			default:
 				return $iterator->get($field) == 0 ? '' : str_replace(',', '.', $iterator->get($field));
 		}
 	}
 	
 	function getFormula( $row, $columnIndex, $cellName )
 	{
 		$fields = $this->getFields();
 	
 		if ( $columnIndex == count($fields) - 1 )
 		{
 			return "=SUM(B".$row.":".PHPExcel_Cell::stringFromColumnIndex($columnIndex-1).$row.")";
 		}
 		else if ( $columnIndex > 0 )
 		{
 			$iterator = $this->getIterator();
 			if ( $iterator->get('Group') > 0 )
 			{
                $groupField = $_REQUEST['group'];
                $groupValue = $iterator->get('ItemId');

                $items = array_filter($this->row_it->getRowset(), function($value) use($groupField, $groupValue) {
                   return $value[$groupField] == $groupValue;
                });

	 			return "=SUM(".$cellName.($row+1).":".$cellName.($row + count($items)).")";
 			}
 		}
 	}

 	function getFields()
 	{
 		$iterator = $this->getIterator();
 		
        $fields = array ( 'ItemId' => $iterator->object->getAttributeUserName('Caption') );

        foreach( $iterator->getDaysMap() as $dayId => $dayName ) {
            $fields['Day'.$dayId] = $dayName;
        }

		$fields = array_merge($fields, array( 'Total' => translate('Итого')) );

 		return $fields;
 	}
 	
	function getWidth( $field )
	{
 		switch ( $field )
 		{
 			case 'ItemId':
 				return $this->getIterator()->get('Group') < 1 ? 60 : 30;

 			case 'Total':
 				return 15;

 			default:
 			    preg_match('/Day(\d+)/', $field, $match);
 			    $map = $this->getIterator()->getDaysMap();
 				return is_numeric($map[$match[1]]) ? 5 : 10;
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
