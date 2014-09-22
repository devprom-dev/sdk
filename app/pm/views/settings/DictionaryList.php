<?php

class DictionaryList extends PMStaticPageList
{
	function IsNeedToDisplay( $attr ) 
	{
		switch ( $attr ) 
		{
			case 'Caption': 
				return true;
		}
		
		return false;
	}

	function drawCell( $object_it, $attr )
	{
	    $session = getSession();
	    
		switch ( $attr )
		{
			case 'Caption':
				echo '<a href="'.$session->getApplicationUrl().'project/dicts?dict='.$object_it->get('ReferenceName').'">'.
					translate($object_it->getDisplayName()).'</a>';
					
				break;
				
			default:
				parent::drawCell( $attr, $object_it );
		}
	}
	
	function getGroupFields()
	{
		return array();
	}
}
