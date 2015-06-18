<?php

class DocumentModeRegistry extends ObjectRegistrySQL
{
    function createSQLIterator( $sql )
    {
        return $this->createIterator( array (
                array ( 
                		'entityId' => 1, 
                		'ReferenceName' => 'view',
                		'Caption' => translate('Просмотр')
                ),
                array ( 
                		'entityId' => 2,
                		'ReferenceName' => 'edit',
                		'Caption' => translate('Редактирование')
                )
        ));
    }
}