<?php

class ScrumIterator extends OrderedIterator
{
 	function getDisplayName() 
 	{
 		$part_it = $this->getRef('Participant');
 		
 		return $this->getDateFormat('RecordCreated').
			' - '.$part_it->getDisplayName();
 	}
}
