<?php

class FieldTaskTypeStateDictionary extends FieldDictionary
{
 	function getOptions()
	{
	    $options = array();

		$this->getObject()->addFilter(new FilterBaseVpdPredicate());
	 	$type_it = $this->getObject()->getAll();
	 	
		while( !$type_it->end() )
		{
		    $options[] = array (
                'value' => $type_it->get('ReferenceName'),
                'caption' => $type_it->getDisplayName()
            );
		    $type_it->moveNext();
		}
		
		return $options;
	}
}