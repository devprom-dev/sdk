<?php

class FieldWikiPageAttributeDictionary extends FieldDictionary
{
	function getOptions()
	{
 		$object = $this->getObject();
 		if ( !is_object($object) ) return array();
 		
        $attributes = array_diff( array_keys($object->getAttributes()),
            array_merge(
                $object->getAttributesByGroup('system'),
                array(
                    'Project', 'UID', 'DocumentId', 'DocumentVersion', 'ParentPage', 'OrderNum', 'Workflow', 'LastTransition'
                )
            )
        );

        $options = array();
 		foreach ( $attributes as $key )
 		{
            if ( !$object->getAttributeEditable($key) ) continue;
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
