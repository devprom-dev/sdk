<?php

include_once "StateBaseIterator.php";

class IssueStateIterator extends StateBaseIterator
{
	function getWarningMessage( $object_it = null )
	{
		switch( $this->get('ReferenceName') )
		{
			case 'submitted':
				break;
		}

		return '';
	}
}
