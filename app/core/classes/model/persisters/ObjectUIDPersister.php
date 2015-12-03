<?php

class ObjectUIDPersister extends ObjectSQLPersister
{
    function getSelectColumns( $alias )
    {
        $alias = $alias != '' ? $alias."." : "";

        $object = $this->getObject();
        $objectPK = $alias.$object->getClassName().'Id';

        return array( " (SELECT ".$objectPK." ) UID " );
    }
}
