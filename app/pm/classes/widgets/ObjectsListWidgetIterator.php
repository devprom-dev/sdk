<?php

class ObjectsListWidgetIterator extends CacheableIterator
{
    function getWidgetIt()
    {
        if ( is_array($this->get('data')) && count($this->get('data')) > 0 ) {
            return $this->object->getWidgetObject($this->get('ReferenceName'))->createCachedIterator(
                array(
                    $this->get('data')
                )
            );
        }
        else {
            return $this->object->getWidgetObject('Module')->getEmptyIterator();
        }
    }
}