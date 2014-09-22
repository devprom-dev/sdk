<?php

class ObjectTemplateIterator extends OrderedIterator
{
 	function getAnchorIt()
 	{
 		global $model_factory;
 		
 		if ( $this->get('ObjectClass') == '' || $this->get('ObjectId') < 1 )
 		{
 			return $this->object->getExact(0);
 		}
 		else
 		{
 			$anchor = $model_factory->getObject($this->get('ObjectClass'));
 			return $anchor->getExact($this->get('ObjectId'));
 		}
 	}
}