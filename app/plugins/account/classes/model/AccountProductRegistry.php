<?php

class AccountProductRegistry extends ObjectRegistrySQL
{
    function createSQLIterator( $sql )
    {
        return $this->createIterator( array (
                array ( 'entityId' => 'LicenseEnterprise', 'Caption' => text('account7') ),
                array ( 'entityId' => 'LicenseTrial', 'Caption' => text('account8') ),
        		array ( 'entityId' => 'LicenseTeam', 'Caption' => text('account9') )
        ));
    }
}