<?php

class TransitionResetFieldIterator extends OrderedIterator
{
 	function getDisplayName() 
 	{
 		$name = translate(getFactory()->getObject($this->get('Entity'))->getAttributeUserName($this->get('ReferenceName')));
 		
 		return $name == '' ? preg_replace('/%1/', $this->get('ReferenceName'), text(1882)) : $name; 
 	}
}
