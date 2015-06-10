<?php

class SmtpEncryptionModeRegistry extends ObjectRegistrySQL
{
    function createSQLIterator( $sql )
    {
        return $this->createIterator( array (
                array ( 'PriorityId' => 'auto', 'Caption' => translate('Нет') ),
        		array ( 'PriorityId' => 'tls', 'Caption' => 'TLS' ),
                array ( 'PriorityId' => 'ssl', 'Caption' => 'SSL' )
        ));
    }
}