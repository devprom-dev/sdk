<?php

include_once "RequestTraceBaseIterator.php";

class RequestTraceMilestoneIterator extends RequestTraceBaseIterator
{
 	function getDisplayName()
 	{
 		if ( $this->get('Deadline') != '' && $this->get('Deadline') != 'NULL' )
 		{
 			return $this->getDateFormatted('Deadline');
 		}
 		else
 		{
 			return parent::getDisplayName();
 		}
 	}
}
