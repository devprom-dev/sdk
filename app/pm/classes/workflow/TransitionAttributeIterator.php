<?php

class TransitionAttributeIterator extends OrderedIterator
{
 	function getDisplayName() 
 	{
 		global $model_factory;
 		
 		$object = $model_factory->getObject($this->get('Entity'));
 		
 		return translate($object->getAttributeUserName( $this->get('ReferenceName') ));
 	}
}
