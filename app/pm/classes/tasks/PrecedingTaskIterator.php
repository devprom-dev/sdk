<?php

class PrecedingTaskIterator extends TaskIterator
{
 	function getDisplayName()
 	{
 		$caption = parent::getDisplayName();
 		
 		if ( $this->getId() > 0 )
 		{
 			$state_it = $this->getStateIt();
 			if ( is_object($state_it) )
 			{
 				$caption .= ' ('.$state_it->getDisplayName().')';
 			}
 		}
 		
 		return $caption;
 	}
}
