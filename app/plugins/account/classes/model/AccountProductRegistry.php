<?php

class AccountProductRegistry extends ObjectRegistrySQL
{
    function createSQLIterator( $sql )
    {
        return $this->createIterator( array (
                array ( 
					'entityId' => 'LicenseEnterprise',
					'Caption' => text('account7'),
					'ValueName' => text('account5'),
                    'PaymentRequired' => false
                ),
                array (
					'entityId' => 'LicenseTrial',
					'Caption' => text('account8'),
					'ValueName' => text('account5'),
                    'PaymentRequired' => false
				),
        		array (
					'entityId' => 'LicenseTeam',
					'Caption' => text('account9'),
                    'PaymentRequired' => false
				)
        ));
    }
}