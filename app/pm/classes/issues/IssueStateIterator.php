<?php

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
