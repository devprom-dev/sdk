<?php

class AttachmentEntityRegistry extends ObjectRegistrySQL
{
    function createSQLIterator($sql_query)
    {
        $objects = array();
        foreach( array('Task','Request','Question','Comment','TestCaseExecution') as $class ) {
            if ( !class_exists($class) ) continue;
            $objects[] = array (
                'entityId' => strtolower($class),
                'Caption' => getFactory()->getObject($class)->getDisplayName()
            );
        }
        $type_it = getFactory()->getObject('WikiType')->getAll();
        while( !$type_it->end() ) {
            if ( !class_exists($type_it->get('ClassName')) ) continue;
            $objects[] = array (
                'entityId' => $type_it->get('ClassName'),
                'Caption' => getFactory()->getObject($type_it->get('ClassName'))->getDisplayName()
            );
            $type_it->moveNext();
        }
        return $this->createIterator($objects);
    }
}