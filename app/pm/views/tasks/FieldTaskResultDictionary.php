<?php

class FieldTaskResultDictionary extends FieldDictionary
{
 	function __construct()
 	{
 		parent::__construct(getFactory()->getObject('TaskType'));
 	}
 	
 	function getOptions()
 	{
		$items = array();

		$result_it = getFactory()->getObject('pm_TestExecutionResult')->getAll();
		while ( !$result_it->end() )
		{
			$items[] = $result_it->getDisplayName();
			$result_it->moveNext();
		}

		$options = array();
		foreach ( $items as $item )
		{
		    $options[] = array (
		        'caption' => $item,
                'value' => $item         
		    );
		}
		return $options;
 	}
}