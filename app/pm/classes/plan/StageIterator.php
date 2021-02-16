<?php

class StageIterator extends OrderedIterator
{
 	function getObjectIt()
	{
        $object = getFactory()->getObject($this->get('State'));
        return $object->getExact( $this->getId() );
	}

    function getDisplayName()
 	{
 		if ( $this->getId() != '' ) {
            $object = getFactory()->getObject($this->get('State'));
 			return $object->getDisplayName().' '.$this->get('Caption');
 		}
 		return $this->getId();
 	}
}
