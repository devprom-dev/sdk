<?php

class CustomizableObjectIterator extends OrderedIterator
{
    function getDisplayName()
    {
        $name = parent::getDisplayName();

        if ( $name == '' ) {
            $name = getFactory()->getObject($this->get('ReferenceName'))->getDisplayName();
        }

        return $name;
    }
}