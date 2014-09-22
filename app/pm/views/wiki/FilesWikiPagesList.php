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
				
		 		if ( $object_it->IsImage('Content')) 
		 		{
					echo '<a class=image_attach href="'.$object_it->getFileUrl().
						'&.png"><img src="/images/image.png"> '.$object_it->getFileName('Content').'</a>'; 		
		 		}
		 		else
		 		{
					echo '<a class=modify_image href="'.$object_it->getFileUrl().
						'"><img src="/images/attach.png"> '.$object_it->getFileName('Content').'</a>'; 		
		 		}
		 		
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