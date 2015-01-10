<?php

class TransitionAttributeIterator extends OrderedIterator
{
 	function getDisplayName() 
 	{
 		$user_name = translate(getFactory()->getObject($this->get('Entity'))->getAttributeUserName($this->get('ReferenceName')));
 		
 		return $user_name == '' ? preg_replace('/%1/', $this->get('ReferenceName'), text(1882)) : $user_name;
 	}
}
