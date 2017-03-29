<?php

class AccountProductScrumBoardRegistry extends ObjectRegistrySQL
{
    function createSQLIterator( $sql )
    {
        return $this->createIterator( array (
        		array ( 
                    'entityId' => 'LicenseScrumBoard-5',
                    'LicenseType' => 'LicenseScrumBoard',
                    'Caption' => text('account45'),
                    'CaptionText' => text('account36'),
                    'AggreementText' => text('account46'),
                    'RequiredFields' => array('AggreementForm','Aggreement'),
                    'ValueName' => text('account4'),
                    'ValueDefault' => 3,
                    'PriceRUB' => 300,
                    'PriceUSD' => 5,
                    'PaymentRequired' => true,
                    'UsersLimit' => 5
        		),
        		array ( 
                    'entityId' => 'LicenseScrumBoard-100',
                    'LicenseType' => 'LicenseScrumBoard',
                    'Caption' => text('account40'),
                    'CaptionText' => text('account36'),
                    'AggreementText' => text('account46'),
                    'RequiredFields' => array('AggreementForm','Aggreement'),
                    'ValueName' => text('account4'),
                    'ValueDefault' => 6,
                    'PriceRUB' => 3600,
                    'PriceUSD' => 60,
                    'PaymentRequired' => true
        		)
        ));
    }
}