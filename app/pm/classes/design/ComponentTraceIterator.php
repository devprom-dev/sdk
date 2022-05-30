<?php

class ComponentTraceIterator extends OrderedIterator
{
    function getDisplayNameReference() {
        return 'ObjectId';
    }

    function getDisplayName()
    {
        $object_it = $this->getRef($this->getDisplayNameReference());

        $uid = new ObjectUID;
        if ( $uid->hasUid($object_it) ) {
            return $uid->getUidWithCaption($object_it, 50);
        }
        else {
            return $object_it->getDisplayName();
        }
    }

    function getObjectIt()
    {
        $className = getFactory()->getClass($this->get('ObjectClass'));
        if ( !class_exists($className) ) return $this->object->getEmptyIterator();

        $object = getFactory()->getObject($className);
        if ( $this->get('ObjectId') == '' ) return $object->getEmptyIterator();

        return $object->getExact( $this->get('ObjectId') );
    }
}