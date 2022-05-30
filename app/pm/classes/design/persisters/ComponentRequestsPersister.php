<?php

class ComponentRequestsPersister extends ObjectSQLPersister
{
    function getAttributes() {
        return array('Requests', 'RequestsCount');
    }

    function getSelectColumns( $alias )
 	{
        $trace = getFactory()->getObject('ComponentTraceRequest');
 		return array(
            " ( SELECT GROUP_CONCAT(CAST(l.ObjectId AS CHAR)) 
                 FROM pm_ComponentTrace l 
                WHERE l.Component = {$this->getPK($alias)}
                  AND l.ObjectClass = '{$trace->getObjectClass()}' ) Requests ",

            " ( SELECT COUNT(l.ObjectId) 
                 FROM pm_ComponentTrace l 
                WHERE l.Component = {$this->getPK($alias)}
                  AND l.ObjectClass = '{$trace->getObjectClass()}' ) RequestsCount ",
 		);
 	}

    function modify($object_id, $parms)
    {
        if ( array_key_exists('Requests', $parms) ) {
            $this->bind($object_id, $parms['Requests']);
        }
        parent::modify($object_id, $parms);
    }

    function add($object_id, $parms)
    {
        if ( $parms['Requests'] != '' ) {
            $this->bind($object_id, $parms['Requests']);
        }
        parent::add($object_id, $parms);
    }

    function bind( $objectId, $requirementId )
    {
        $trace = getFactory()->getObject('ComponentTraceRequest');
        if ( $requirementId == '' ) {
            $bindIt = $trace->getByRef('Component', $objectId);
            while( !$bindIt->end() ) {
                $bindIt->object->delete($bindIt->getId());
                $bindIt->moveNext();
            }
        }
        else {
            $trace->getRegistry()->Merge(
                array(
                    'Component' => $objectId,
                    'ObjectId' => $requirementId
                )
            );
        }
    }
}
