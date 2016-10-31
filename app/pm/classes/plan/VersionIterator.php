<?php

class VersionIterator extends OrderedIterator
{
 	function getObjectIt()
	{
        if ( $this->get('Build') > 0 ) {
            return getFactory()->getObject('Build')->getExact( $this->get('Build') );
        }
        if ( $this->get('Release') > 0 ) {
			return getFactory()->getObject('Iteration')->getExact( $this->get('Release') );
		}
		if ( $this->get('Version') > 0 ) {
			return getFactory()->getObject('Release')->getExact( $this->get('Version') );
		}
		return $this->object->getEmptyIterator();
	}

	function getEditUrl() {
	    $object_it = $this->getObjectIt();
        if ( $object_it->getId() == '' ) return '?';
        return $this->getObjectIt()->getEditUrl();
    }
}
