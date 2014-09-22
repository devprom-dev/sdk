<?php

class UserParticipanceTypeRegistry extends ObjectRegistrySQL
{
    function createSQLIterator( $sql )
    {
        global $model_factory;
        
        $data = array (
                array ( 'entityId' => 1, 'ReferenceName' => 'participant', 'Caption' => translate('Участники проекта') )
        );
        
        if ( $model_factory->getObject('User')->getAttributeType('GroupId') != '' )
        {
            $data[] = array ( 'entityId' => 2, 'ReferenceName' => 'linked', 'Caption' => translate('Участники связанных проектов') );
        }
        
        $data[] = array ( 'entityId' => 3, 'ReferenceName' => 'guest', 'Caption' => text(1370) ); 
        
        return $this->createIterator($data);
    }
}