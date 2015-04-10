<?php

class AccountProductSaasRegistry extends ObjectRegistrySQL
{
    function createSQLIterator( $sql )
    {
        return $this->createIterator( array (
        		array ( 
        				'entityId' => 'LicenseSAASALM', 
        				'Caption' => text('account10'),
        				'RequiredFields' => array('AggreementForm','Aggreement','PaymentServiceInfo'),
        				'ValueName' => text('account4'),
        				'ValueDefault' => 12,
        				'PriceRUB' => 3000,
        				'PriceUSD' => 50
        		),
        		array ( 
        				'entityId' => 'LicenseSAASALMMiddle', 
        				'Caption' => text('account11'),
        				'RequiredFields' => array('AggreementForm','Aggreement','PaymentServiceInfo'),
        				'ValueName' => text('account4'),
        				'ValueDefault' => 12,
        				'PriceRUB' => 16000,
        				'PriceUSD' => 500
        		),
        		array ( 
        				'entityId' => 'LicenseSAASALMLarge',
        				'Caption' => text('account12'),
        				'RequiredFields' => array('AggreementForm','Aggreement','PaymentServiceInfo'),
        				'ValueName' => text('account4'),
        				'ValueDefault' => 12,
        				'PriceRUB' => 65000,
        				'PriceUSD' => 2000
        		)
        ));
    }
}