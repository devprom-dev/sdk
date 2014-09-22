<?php

class CustomAttributeTypeClassNameField extends FieldDictionary
{
 	function getOptions()
	{
		$objects = array('participant', 'request');
		
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		
		if ( $methodology_it->HasTasks() )
		{
			$objects[] = 'task';
		}
		
		if ( $methodology_it->HasPlanning() )
		{
			$objects[] = 'iteration';
		}
		
		if ( $methodology_it->HasReleases() )
		{
			$objects[] = 'release';
		}
		
		foreach( $objects as $value )
		{
		    $options[] = array (
                'value' => $value,
                'caption' => getFactory()->getObject($value)->getDisplayName(),
                'disabled' => false
            );
		}
		
		return $options;
	}
}