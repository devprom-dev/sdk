<?php

class AccountProductRegistry extends ObjectRegistrySQL
{
    function createSQLIterator( $sql )
    {
        return $this->createIterator( array (
                array ( 
					'entityId' => 'LicenseEnterprise',
                    'LicenseType' => 'LicenseEnterprise',
					'Caption' => text('account7'),
					'ValueName' => text('account5'),
                    'PaymentRequired' => false
                ),
                array (
					'entityId' => 'LicenseTrial',
                    'LicenseType' => 'LicenseTrial',
					'Caption' => text('account8'),
					'ValueName' => '',
                    'PaymentRequired' => false
				),
        		array (
					'entityId' => 'LicenseTeam',
                    'LicenseType' => 'LicenseTeam',
					'Caption' => text('account9'),
                    'ValueName' => '',
                    'PaymentRequired' => false
				)
        ));
    }
}