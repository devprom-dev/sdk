<?php

class AccountProductDevOpsRegistry extends ObjectRegistrySQL
{
    function createSQLIterator( $sql )
    {
        return $this->createIterator( array (
        		array ( 
                    'entityId' => 'LicenseDevOpsBoard',
                    'Caption' => text('account26'),
                    'CaptionText' => text('account34'),
                    'RequiredFields' => array('AggreementForm','Aggreement','PaymentServiceInfo'),
                    'ValueName' => text('account4'),
                    'ValueDefault' => 3,
                    'PriceRUB' => 12000,
                    'PriceUSD' => 10,
                    'PaymentRequired' => true,
                    'UsersLimit' => 10
        		),
        		array ( 
                    'entityId' => 'LicenseDevOpsBoardUnlimited',
                    'Caption' => text('account29'),
                    'CaptionText' => text('account34'),
                    'RequiredFields' => array('AggreementForm','Aggreement','PaymentServiceInfo'),
                    'ValueName' => text('account4'),
                    'ValueDefault' => 6,
                    'PriceRUB' => 12000,
                    'PriceUSD' => 100,
                    'PaymentRequired' => true
        		)
        ));
    }
}