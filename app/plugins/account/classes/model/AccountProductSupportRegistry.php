<?php

class AccountProductSupportRegistry extends ObjectRegistrySQL
{
    function createSQLIterator( $sql )
    {
        return $this->createIterator( array (
			array (
				'entityId' => 'LicenseTeamSupported',
                'LicenseType' => 'LicenseTeamSupported',
				'Caption' => text('account38'),
				'CaptionText' => ' за 2190 руб.',
				'AggreementText' => text('account41'),
				'RequiredFields' => array('AggreementForm','Aggreement'),
				'ValueDefault' => 365,
				'PriceRUB' => 6,
                'PaymentRequired' => true
			),
			array (
				'entityId' => 'LicenseTeamSupportedCompany',
                'LicenseType' => 'LicenseTeamSupportedCompany',
				'Caption' => text('account39'),
				'CaptionText' => ' за 58400 руб.',
				'AggreementText' => text('account41'),
				'RequiredFields' => array('AggreementForm','Aggreement'),
				'ValueDefault' => 365,
				'PriceRUB' => 160,
                'PaymentRequired' => true
			),
			array (
				'entityId' => 'LicenseTeamSupportedUnlimited',
                'LicenseType' => 'LicenseTeamSupportedUnlimited',
				'Caption' => text('account40'),
				'CaptionText' => ' за 97090 руб.',
				'AggreementText' => text('account41'),
				'RequiredFields' => array('AggreementForm','Aggreement'),
				'ValueDefault' => 365,
				'PriceRUB' => 266,
                'PaymentRequired' => true
			)
        ));
    }
}