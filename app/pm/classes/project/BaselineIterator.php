<?php

class BaselineIterator extends OrderedIterator
{
    function getStageIt()
    {
        $iterationId = intval(substr($this->getId(), -8));
        if ( $iterationId > 0 ) {
            return getFactory()->getObject('Iteration')->getExact($iterationId);
        }

        $releaseId = intval(substr($this->getId(), 0, 8));
        if ( $releaseId > 0 ) {
            return getFactory()->getObject('Release')->getExact($releaseId);
        }

        return $this->object->getEmptyIterator();
    }
}
