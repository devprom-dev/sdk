<?php

class ScrumIterator extends OrderedIterator
{
 	function getDisplayName() 
 	{
 		$part_it = $this->getRef('Participant');
 		
 		return $this->getDateFormatted('RecordCreated').
			' - '.$part_it->getDisplayName();
 	}
}
