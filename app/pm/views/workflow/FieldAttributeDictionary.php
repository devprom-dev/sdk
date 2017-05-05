<?php

class FieldAttributeDictionary extends FieldDictionary
{
	function getOptions()
	{
 		$object = $this->getObject();
 		
        $system_attributes = array_merge(
            $object->getAttributesByGroup('system'),
            $object->getAttributesByGroup('non-form'),
            $object->getAttributesByGroup('workflow')
        );

        $options = array();
 		foreach ( $object->getAttributes() as $key => $attribute )
 		{
 			if ( $key == 'Project' || $key == 'RecordCreated' || $key == 'RecordModified' ) continue;
 			if ( in_array($key, $system_attributes) ) continue;
 			
 			$title = translate($object->getAttributeUserName($key));
 			if ( $title == '' ) continue;
 			
 			$options[$key] = $title;
 		}
 		asort($options);
	    
 		$result = array();
 		foreach( $options as $key => $value ) {
 		    $result[] = array (
                'value' => $key,
                'caption' => $value
            );
 		}
 		return $result;
	}
}
