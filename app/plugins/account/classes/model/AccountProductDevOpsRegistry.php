<?php

class AccountProductDevOpsRegistry extends ObjectRegistrySQL
{
    function createSQLIterator( $sql )
    {
        return $this->createIterator( array (
        		array ( 
        				'entityId' => 'LicenseDevOpsBoard', 
        				'Caption' => text('account26'),
        				'RequiredFields' => array('AggreementForm','Aggreement','PaymentServiceInfo'),
        				'ValueName' => text('account4'),
        				'ValueDefault' => 12,
        				'PriceRUB' => 12000,
        				'PriceUSD' => 60
        		),
        		array ( 
        				'entityId' => 'LicenseDevOpsBoardUnlimited', 
        				'Caption' => text('account29'),
        				'RequiredFields' => array('AggreementForm','Aggreement','PaymentServiceInfo'),
        				'ValueName' => text('account4'),
        				'ValueDefault' => 12,
        				'PriceRUB' => 12000,
        				'PriceUSD' => 290
        		)
        ));
    }
}