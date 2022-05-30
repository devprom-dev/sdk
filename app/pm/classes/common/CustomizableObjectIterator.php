<?php

class CustomizableObjectIterator extends CacheableIterator
{
    function getDisplayName()
    {
        $name = parent::getDisplayName();

        if ( $name == '' && $this->get('ReferenceName') != '' ) {
            $name = getFactory()->getObject($this->get('ReferenceName'))->getDisplayName();
        }

        return $name;
    }
}