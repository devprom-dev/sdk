<?php

class DocumentSectionNumberingModeRegistry extends ObjectRegistrySQL
{
    function createSQLIterator( $sql )
    {
        return $this->createIterator( array (
                array ( 'entityId' => '1', 'ReferenceName' => 'display', 'Caption' => translate('Вкл.') ),
        ));
    }
}