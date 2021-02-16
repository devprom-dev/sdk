<?php

class AttachmentEntityRegistry extends ObjectRegistrySQL
{
    function createSQLIterator($sql_query)
    {
        $objects = array();
        foreach( array('Task','Request','Question','Comment','TestCaseExecution','Issue') as $class ) {
            if ( !class_exists($class) ) continue;
            $object = getFactory()->getObject($class);
            $title = $object->getDisplayName();
            if ( $class == 'Request' && getSession()->IsRDD() && class_exists('Increment') ) {
                $title = getFactory()->getObject('Increment')->getDisplayName();
            }
            $objects[] = array (
                'entityId' => get_class($object),
                'Caption' => $title
            );
        }
        $type_it = getFactory()->getObject('WikiType')->getAll();
        while( !$type_it->end() ) {
            if ( !class_exists($type_it->get('ClassName')) ) {
                $type_it->moveNext();
                continue;
            }
            $object = getFactory()->getObject($type_it->get('ClassName'));
            $objects[] = array (
                'entityId' => get_class($object),
                'Caption' => $object->getDisplayName()
            );
            $type_it->moveNext();
        }
        return $this->createIterator($objects);
    }
}