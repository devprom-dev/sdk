<?php

class AccountProductDevOpsRegistry extends ObjectRegistrySQL
{
    function createSQLIterator( $sql )
    {
        return $this->createIterator( array (
            array (
                'entityId' => 'LicenseDevOpsBoard',
                'LicenseType' => 'LicenseDevOpsBoard',
                'Caption' => text('account26'),
                'CaptionText' => text('account53'),
                'AggreementText' => text('account54'),
                'RequiredFields' => array('AggreementForm','Aggreement'),
                'ValueName' => text('account4'),
                'ValueDefault' => 3,
                'PriceRUB' => 300,
                'PriceUSD' => 5,
                'PaymentRequired' => true,
                'UsersLimit' => 3
            ),
            array (
                'entityId' => 'LicenseDevOpsBoard-10',
                'LicenseType' => 'LicenseDevOpsBoard',
                'Caption' => text('account52'),
                'CaptionText' => text('account53'),
                'AggreementText' => text('account54'),
                'RequiredFields' => array('AggreementForm','Aggreement'),
                'ValueName' => text('account4'),
                'ValueDefault' => 3,
                'PriceRUB' => 3600,
                'PriceUSD' => 49,
                'PaymentRequired' => true,
                'UsersLimit' => 10
            ),
            array (
                'entityId' => 'LicenseDevOpsBoardUnlimited',
                'LicenseType' => 'LicenseDevOpsBoardUnlimited',
                'Caption' => text('account29'),
                'CaptionText' => text('account53'),
                'AggreementText' => text('account54'),
                'RequiredFields' => array('AggreementForm','Aggreement'),
                'ValueName' => text('account4'),
                'ValueDefault' => 6,
                'PriceRUB' => 20990,
                'PriceUSD' => 350,
                'PaymentRequired' => true
            )
        ));
    }
}