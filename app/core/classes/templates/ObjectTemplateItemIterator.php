<?php

class ObjectTemplateItemIterator extends OrderedIterator
{
 	function getAnchorIt()
 	{
 		if ( $this->get('ObjectClass') == '' || !class_exists($this->get('ObjectClass')) || $this->get('ObjectId') < 1 ) {
 			return $this->object->getEmptyIterator();
 		}
 		else {
 			return getFactory()->getObject($this->get('ObjectClass'))->getExact($this->get('ObjectId'));
 		}
 	}
}