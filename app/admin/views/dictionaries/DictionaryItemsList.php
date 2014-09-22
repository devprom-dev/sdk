<?php

class DictionaryItemsList extends PageList
{
	function drawCell( $object_it, $attr )
	{
		switch ( $attr )
		{
			case 'Caption':
				echo $object_it->getDisplayName();
				break;

			case 'DateFormatClass':
				
			    $class_name = $object_it->get($attr);
				
				if ( class_exists($class_name) )
				{
					$format = new $class_name;
					
					echo $format->getDisplayName();
					
					break;
				}
				
				parent::drawCell( $object_it, $attr );
				
				break;
				
			default:
				parent::drawCell( $object_it, $attr );
		}
	}

	function getGroupFields()
	{
		return array();
	}
}
