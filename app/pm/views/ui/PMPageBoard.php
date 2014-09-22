<?php

class PMPageBoard extends PageBoard
{
    function PMPageBoard( $object )
    {
        parent::PageBoard( $object );
    }
    
	function getGroupFields()
	{
	    return array_diff(parent::getGroupFields(), $this->getObject()->getAttributesByGroup('trace')); 
	}
}
