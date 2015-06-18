<?php

class FilesWikiPagesList extends PMStaticPageList
{
	function getColumns()
	{
		$this->object->addAttribute('Size', '', translate('Размер'), true);
		$this->object->addAttribute('LastChange', '', translate('Последнее изменение'), true);
		
		return parent::getColumns();
	}

	function IsNeedToDisplay( $attr ) 
	{
		switch( $attr )
		{
			case 'Caption':
			case 'Size':
			case 'WikiPage':
			case 'LastChange':
				return true;
		}
		
		return false;
	}
	
	function drawCell( $object_it, $attr )
	{
		switch( $attr )
		{
			case 'Caption':
				
		 		echo $object_it->getFileLink(); 
		 		
		 		break;

			case 'LastChange':
				
				echo $object_it->getDateTimeFormat('RecordModified');
				
				break;

			case 'Size':

				echo $object_it->getFileSizeKb('Content');
				echo ' KB';
				
				break;
	
		 	default:
		 		parent::drawCell( $object_it, $attr );
		}
	}
	
	function getGroupDefault()
	{
		return '';
	}
}