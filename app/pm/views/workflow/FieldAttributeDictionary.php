<?php

class FieldAttributeDictionary extends FieldDictionary
{
	function getOptions()
	{
 		$object = $this->getObject();
 		
 		$attributes = $object->getAttributes();
 		
 		$options = array();

 		$system_attributes = $this->getObject()->getAttributesByGroup('system');
 		
 		$transition_attributes = $this->getObject()->getAttributesByGroup('transition');
 		
 		foreach ( $attributes as $key => $attribute ) 
 		{
 			if ( $key == 'Project' || $key == 'RecordCreated' || $key == 'RecordModified' ) continue;
 			
 			if ( in_array($key, $system_attributes) ) continue;
 			
 			if ( !in_array($key, $transition_attributes) && !$object->IsAttributeStored($key) && $object->getAttributeOrigin($key) != 'custom' ) continue;
 			
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
