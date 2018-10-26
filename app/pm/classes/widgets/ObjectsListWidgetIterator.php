<?php

class ObjectsListWidgetIterator extends OrderedIterator
{
    function getWidgetIt()
    {
        switch( $this->get('ReferenceName') ) {
            case 'PMReport':
                return getFactory()->getObject('PMReport')->getExact($this->getId());
            case 'Module':
                return getFactory()->getObject('Module')->getExact($this->getId());
        }
        return getFactory()->getObject('Module')->getEmptyIterator();
    }
}