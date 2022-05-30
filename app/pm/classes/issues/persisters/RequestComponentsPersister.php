<?php

class RequestComponentsPersister extends ObjectSQLPersister
{
    function getAttributes() {
        return array(
            'Components'
        );
    }

    function getSelectColumns( $alias )
 	{
        $trace = getFactory()->getObject('ComponentTraceRequest');
 		return array(
            " ( SELECT GROUP_CONCAT(CAST(l.Component AS CHAR)) 
                 FROM pm_ComponentTrace l 
                WHERE l.ObjectId = {$this->getPK($alias)}
                  AND l.ObjectClass = '{$trace->getObjectClass()}' ) Components ",
 		);
 	}
}
