<?php

class PrecedingTaskIterator extends TaskIterator
{
 	function getDisplayName()
 	{
 		$caption = parent::getDisplayName();
 		if ( $this->get('StateName') != '' ) {
			$caption .= ' ('.$this->get('StateName').')';
 		}
 		return $caption;
 	}
}
