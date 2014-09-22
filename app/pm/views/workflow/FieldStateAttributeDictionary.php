<?php

class FieldStateAttributeDictionary extends FieldDictionary
{
	function getOptions()
	{
 		$object = $this->getObject();
 		
 		$attributes = $object->getAttributes();
 		
 		$options = array();

 		$system_attributes = $this->getObject()->getAttributesByGroup('system');
 		
 		foreach ( $attributes as $key => $attribute ) 
 		{
 			if ( $key == 'Project' ) continue;
 			
 			if ( in_array($key, $system_attributes) ) continue;
 			
 			$title = translate($object->getAttributeUserName($key));
 			
 			if ( $title == '' ) continue;
 			
 			$options[$key] = $title;
 		}
 		
 		asort($options);
	    
 		$result = array();
 		
 		foreach( $options as $key => $value )
 		{
 		    $result[] = array (
                'value' => $key,
                'caption' => $value
            );
 		}
 		
 		return $result;
	}
}
