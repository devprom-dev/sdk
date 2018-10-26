<?php

class CustomAttributeTypeClassNameField extends FieldDictionary
{
 	function getOptions()
	{
		$objects = array('user' => '', 'priority' => '', 'importance' => '');
		foreach( $objects as $className => $name ) {
            $objects[$className] = getFactory()->getObject($className)->getDisplayName();
        }

        $object_it = getFactory()->getObject('CustomizableObjectSet')->getAll();
        while ( !$object_it->end() )
        {
            if ( strpos($object_it->getId(), ':') > 0 ) {
                $object_it->moveNext();
                continue;
            }
            $objects[strtolower($object_it->getId())] = $object_it->getDisplayName();
            $object_it->moveNext();
        }

		foreach( $objects as $className => $name )
		{
		    $options[] = array (
                'value' => $className,
                'caption' => $name,
                'disabled' => false
            );
		}
		
		return $options;
	}
}