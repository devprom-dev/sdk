<?php

class AccountProductSaasRegistry extends ObjectRegistrySQL
{
    function createSQLIterator( $sql )
    {
        return $this->createIterator( array (
        		array ( 'entityId' => 'LicenseSAASALM', 'Caption' => text('account10') ),
        		array ( 'entityId' => 'LicenseSAASALMMiddle', 'Caption' => text('account11') ),
        		array ( 'entityId' => 'LicenseSAASALMLarge', 'Caption' => text('account12') )
        ));
    }
}