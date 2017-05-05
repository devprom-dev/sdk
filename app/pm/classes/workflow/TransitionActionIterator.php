<?php

class TransitionActionIterator extends OrderedIterator
{
 	function getDisplayName() 
 	{
 		$name = $this->getRef('ReferenceName', getFactory()->getObject('StateBusinessAction'))->getDisplayName();
 		return $name != '' ? $name : text(2008);
 	}
}
