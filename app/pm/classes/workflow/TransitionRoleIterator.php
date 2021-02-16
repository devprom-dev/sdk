<?php

class TransitionRoleIterator extends CacheableIterator
{
 	function getDisplayName() 
 	{
 		$stage_it = $this->getRef('ProjectRole');
		return $stage_it->getId() != ''
                    ? $stage_it->getDisplayName()
                    : text(3018);
 	}
}
