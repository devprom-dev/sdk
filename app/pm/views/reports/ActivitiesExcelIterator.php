<?php
include_once SERVER_ROOT_PATH.'core/classes/export/IteratorExportExcel.php';
 
class ActivitiesExcelIterator extends IteratorExportExcel
{
 	private $row_it;
 	
 	function __construct( $iterator )
 	{
 		$ids = $iterator->fieldToArray('ItemId');
 		if ( count($ids) < 1 ) $ids = array(0);
 		
 		$this->row_it = $this->getRowsObject()->getRegistry()->Query(
                array (
                    new FilterInPredicate($ids)
                )
 			);
 		$this->group_it = $this->getGroupObject()->getAll();
 		
 		$iterator->moveFirst();
 		parent::__construct( $iterator );
 	}

	function getRowsObject()
	{
		if ( is_object($this->rows_object) ) return $this->rows_object;
		return getFactory()->getObject($_REQUEST['rowsobject']);
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
 		switch ( $field )
 		{
 			case 'ItemId':
 				if ( $iterator->get('Group') > 0 ) {
                    $this->group_it->moveToId($iterator->get('ItemId'));
                    return $this->group_it->getDisplayName();
 				} 
 				else {
                    $this->row_it->moveToId($iterator->get('ItemId'));
                    $info = $this->getUidService()->getUidInfo($this->row_it, true);
                    $title = ($info['uid'] != '' ? '['.$info['uid'].'] ' : '') . html_entity_decode($info['caption']);
                    if ( $info['state_name'] != '' ) $title .= ' ('.$info['state_name'].')';
                    return $title;
 				}
 				break;

 			default:
 				return parent::get($field);
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
