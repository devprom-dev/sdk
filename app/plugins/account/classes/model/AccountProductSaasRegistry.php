<?php

class AccountProductSaasRegistry extends ObjectRegistrySQL
{
    function createSQLIterator( $sql )
    {
        return $this->createIterator( array (
        		array ( 
        				'entityId' => 'LicenseSAASALM', 
        				'Caption' => text('account10'),
						'CaptionText' => text('account34'),
        				'RequiredFields' => array('AggreementForm','Aggreement','PaymentServiceInfo'),
        				'ValueName' => text('account4'),
        				'ValueDefault' => 12,
						'PriceRUB' => 800,
						'PriceUSD' => 15,
						'Options' => array (
                            array (
                                'OptionId' => 'agile',
                                'Caption' => text('account37')
                            ),
							array (
								'OptionId' => 'support',
								'Caption' => text('account30'),
								'PriceRUB' => 600,
								'PriceUSD' => 10
							),
							array (
								'OptionId' => 'reqs',
								'Caption' => text('account31'),
								'PriceRUB' => 600,
								'PriceUSD' => 10
							),
							array (
								'OptionId' => 'qa',
								'Caption' => text('account32'),
								'PriceRUB' => 500,
								'PriceUSD' => 8
							),
							array (
								'OptionId' => 'docs',
								'Caption' => text('account33'),
								'PriceRUB' => 500,
								'PriceUSD' => 8
							)
						)
        		),
        		array ( 
        				'entityId' => 'LicenseSAASALMMiddle', 
        				'Caption' => text('account11'),
						'CaptionText' => text('account34'),
        				'RequiredFields' => array('AggreementForm','Aggreement','PaymentServiceInfo'),
        				'ValueName' => text('account4'),
        				'ValueDefault' => 12,
						'PriceRUB' => 4200,
						'PriceUSD' => 75,
						'Options' => array (
                            array (
                                'OptionId' => 'agile',
                                'Caption' => text('account37')
                            ),
							array (
								'OptionId' => 'support',
								'Caption' => text('account30'),
								'PriceRUB' => 3200,
								'PriceUSD' => 55
							),
							array (
								'OptionId' => 'reqs',
								'Caption' => text('account31'),
								'PriceRUB' => 3200,
								'PriceUSD' => 55
							),
							array (
								'OptionId' => 'qa',
								'Caption' => text('account32'),
								'PriceRUB' => 2600,
								'PriceUSD' => 52
							),
							array (
								'OptionId' => 'docs',
								'Caption' => text('account33'),
								'PriceRUB' => 2600,
								'PriceUSD' => 52
							)
						)
        		),
        		array ( 
        				'entityId' => 'LicenseSAASALMLarge',
        				'Caption' => text('account12'),
						'CaptionText' => text('account34'),
        				'RequiredFields' => array('AggreementForm','Aggreement','PaymentServiceInfo'),
        				'ValueName' => text('account4'),
        				'ValueDefault' => 12,
        				'PriceRUB' => 18000,
        				'PriceUSD' => 320,
						'Options' => array (
                            array (
                                'OptionId' => 'agile',
                                'Caption' => text('account37')
                            ),
                            array (
                                'OptionId' => 'support',
                                'Caption' => text('account30'),
                                'PriceRUB' => 13000,
                                'PriceUSD' => 230
                            ),
                            array (
                                'OptionId' => 'reqs',
                                'Caption' => text('account31'),
                                'PriceRUB' => 13000,
                                'PriceUSD' => 230
                            ),
                            array (
                                'OptionId' => 'qa',
                                'Caption' => text('account32'),
                                'PriceRUB' => 11000,
                                'PriceUSD' => 200
                            ),
                            array (
                                'OptionId' => 'docs',
                                'Caption' => text('account33'),
                                'PriceRUB' => 11000,
                                'PriceUSD' => 200
                            )
                        )
        		)
        ));
    }
}