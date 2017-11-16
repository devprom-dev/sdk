<?php

class ArtefactList extends PMPageList
{
	function getPredicates( $values )
	{
		return array (
			new FilterAttributePredicate( 'Kind', $values['artefacttype'] ),
			new FilterAttributePredicate( 'Version', $values['version'] ) 
		);
	}
		
	function getColumns()
	{
		$this->object->addAttribute('Size', '', translate('Размер'), true);
		
		return parent::getColumns();
	}

	function IsNeedToDisplay( $attr ) 
	{
		switch($attr) {
			case 'Content':
			case 'Kind':
			case 'Project':
				return false;
				
			case 'RecordModified':
				return true;
				
			default:
				return parent::IsNeedToDisplay($attr);
		}
	}

	function drawCell( $object_it, $attr ) 
	{
		switch($attr)
		{
			case 'Size':
				echo $object_it->getFileSizeKb('Content').' Kb';
				break;

			default:
				parent::drawCell( $object_it, $attr );
		}
	}
	
	function getGroupDefault()
	{
		return 'Kind';
	}
}