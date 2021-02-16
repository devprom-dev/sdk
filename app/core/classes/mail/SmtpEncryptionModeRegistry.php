<?php

class SmtpEncryptionModeRegistry extends ObjectRegistrySQL
{
    function createSQLIterator( $sql )
    {
        return $this->createIterator( array (
                array ( 'entityId' => 'auto', 'Caption' => translate('Нет') ),
        		array ( 'entityId' => 'tls', 'Caption' => 'TLS' ),
                array ( 'entityId' => 'ssl', 'Caption' => 'SSL' )
        ));
    }
}