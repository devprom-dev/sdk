<?php

class ActivityIterator extends OrderedIterator
{
 	function getDisplayName()
 	{
 		$result = $this->getDateFormat('ReportDate');
 		
		$result .= ' ['.getSession()->getLanguage()->getHoursWording($this->get('Capacity')).']';
		
		$part_it = $this->getRef('Participant');
		
		if ( $part_it->getId() > 0 )
			$result .= ' ('.$part_it->getDisplayName().')';
		
		if ( $this->get('Description') != '' )
			$result .= ' '.$this->getHtml('Description');
 		
 		return $result;
 	}

  	function getDisplayNameShort()
 	{
 		$result = $this->getDateFormat('ReportDate');
 		
		$result .= ' ['.getSession()->getLanguage()->getHoursWording($this->get('Capacity')).']';
		
		if ( $this->get('Description') != '' )
			$result .= ' '.$this->get('Description');
 		
 		return $result;
 	}
}
