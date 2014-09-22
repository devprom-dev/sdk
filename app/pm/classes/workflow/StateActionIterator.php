<?php

class StateActionIterator extends OrderedIterator
{
 	function getDisplayName() 
 	{
 		global $model_factory;
 		
 		$action_it = $this->getRef('ReferenceName', $model_factory->getObject('StateBusinessAction'));
 			
 		return $action_it->getDisplayName();
 	}
}
