<?php

class ObjectSearchablePersister extends ObjectSQLPersister
{
    function add($object_id, $parms) {
        $this->staleSearchable( $object_id );
    }

    function modify($object_id, $parms) {
        if ( count(array_intersect(array_keys($parms), $this->getAttributes())) < 1 ) return;
        $this->staleSearchable( $object_id );
    }

    function staleSearchable( $object_id ) {
        DAL::Instance()->Query(
            "INSERT INTO pm_Searchable (ObjectId, ObjectClass, RecordCreated, RecordModified)
               VALUES ({$object_id}, '{$this->getClassName()}', NOW(), NOW()) 
               ON DUPLICATE KEY UPDATE RecordModified = NOW(), IsActive = 'N'"
        );
    }

    function afterDelete($object_it) {
        DAL::Instance()->Query(
            "DELETE FROM pm_Searchable WHERE ObjectId = {$object_it->getId()} AND ObjectClass = '{$this->getClassName()}'"
        );
    }

    function getClassName() {
        return get_class($this->getObject());
    }

    function IsPersisterImportant() {
        return true;
    }
}
