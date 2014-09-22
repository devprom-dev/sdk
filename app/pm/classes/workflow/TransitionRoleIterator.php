<?php

class TransitionRoleIterator extends OrderedIterator
{
 	function getDisplayName() 
 	{
 		$stage_it = $this->getRef('ProjectRole');
		return $stage_it->getDisplayName();
 	}
}
