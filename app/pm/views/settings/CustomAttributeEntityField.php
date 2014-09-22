<?php

class CustomAttributeEntityField extends FieldDictionary
{
 	function getOptions()
	{
		$object_it = getFactory()->getObject('CustomizableObjectSet')->getAll();
		
		$keys = array();

		while ( !$object_it->end() )
		{
			$keys[$object_it->getId()] = $object_it->get('Caption');
			
			$object_it->moveNext();
		}

		asort($keys);
		
		$options = array();
	    
 		foreach( $keys as $key => $value )
		{
		    $options[] = array (
                'value' => $key,
                'caption' => translate($value),
                'disabled' => false
            );
		}
		
		return $options;
	}
}