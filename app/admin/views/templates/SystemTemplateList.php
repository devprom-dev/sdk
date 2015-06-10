<?php

class SystemTemplateList extends PageList
{
	function drawCell( $object_it, $attr )
	{
		switch ( $attr )
		{
		    case 'Status':
		    	if ( file_exists($object_it->getFilePath()) )
		    	{
		    		echo '<span class="label label-success">'.translate('Пользовательский').'</span>';
		    	}
		    	else
		    	{
		    		echo '<span class="label">'.translate('Системный').'</span>';
		    	}
		    	break;
		    	
			default:
				return parent::drawCell( $object_it, $attr );
		}
	}
}
