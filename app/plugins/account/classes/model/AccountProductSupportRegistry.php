<?php

class AccountProductSupportRegistry extends ObjectRegistrySQL
{
    function createSQLIterator( $sql )
    {
        return $this->createIterator( array (
			array (
				'entityId' => 'LicenseTeamSupported',
				'Caption' => text('account38'),
				'CaptionText' => ' за 2190 руб.',
				'AggreementText' => text('account41'),
				'RequiredFields' => array('AggreementForm','Aggreement','PaymentServiceInfo'),
				'ValueDefault' => 365,
				'PriceRUB' => 6
			),
			array (
				'entityId' => 'LicenseTeamSupportedCompany',
				'Caption' => text('account39'),
				'CaptionText' => ' за 58400 руб.',
				'AggreementText' => text('account41'),
				'RequiredFields' => array('AggreementForm','Aggreement','PaymentServiceInfo'),
				'ValueDefault' => 365,
				'PriceRUB' => 160
			),
			array (
				'entityId' => 'LicenseTeamSupportedUnlimited',
				'Caption' => text('account40'),
				'CaptionText' => ' за 97090 руб.',
				'AggreementText' => text('account41'),
				'RequiredFields' => array('AggreementForm','Aggreement','PaymentServiceInfo'),
				'ValueDefault' => 365,
				'PriceRUB' => 266
			)
        ));
    }
}