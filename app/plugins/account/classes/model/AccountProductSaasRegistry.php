<?php

class AccountProductSaasRegistry extends ObjectRegistrySQL
{
    function createSQLIterator( $sql )
    {
        return $this->createIterator( array (
        		array ( 
                    'entityId' => 'LicenseSAASALM',
                    'Caption' => text('account10'),
                    'CaptionText' => '',
                    'RequiredFields' => array('AggreementForm','Aggreement'),
                    'ValueName' => text('account4'),
                    'ValueDefault' => 12,
                    'PaymentRequired' => true,
                    'PriceRUB' => 0,
                    'PriceUSD' => 0,
                    'Options' => array (
                        array (
                            'OptionId' => 'agile',
                            'Caption' => text('account37'),
                            'PriceRUB' => 800,
                            'PriceUSD' => 15,
                            'Required' => true
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
                    'entityId' => 'LicenseSAASALM20',
                    'Caption' => text('account42'),
                    'CaptionText' => '',
                    'RequiredFields' => array('AggreementForm','Aggreement'),
                    'ValueName' => text('account4'),
                    'ValueDefault' => 12,
                    'PriceRUB' => 0,
                    'PriceUSD' => 0,
                    'PaymentRequired' => true,
                    'Options' => array (
                            array (
                                'OptionId' => 'agile',
                                'Caption' => text('account37'),
                                'PriceRUB' => 2500,
                                'PriceUSD' => 45,
                                'Required' => true
                            ),
                            array (
                                    'OptionId' => 'support',
                                    'Caption' => text('account30'),
                                    'PriceRUB' => 1900,
                                    'PriceUSD' => 33
                            ),
                            array (
                                    'OptionId' => 'reqs',
                                    'Caption' => text('account31'),
                                    'PriceRUB' => 1900,
                                    'PriceUSD' => 33
                            ),
                            array (
                                    'OptionId' => 'qa',
                                    'Caption' => text('account32'),
                                    'PriceRUB' => 1550,
                                    'PriceUSD' => 30
                            ),
                            array (
                                    'OptionId' => 'docs',
                                    'Caption' => text('account33'),
                                    'PriceRUB' => 1550,
                                    'PriceUSD' => 30
                            )
                    )
				),
        		array (
                    'entityId' => 'LicenseSAASALMMiddle',
                    'Caption' => text('account11'),
                    'CaptionText' => '',
                    'RequiredFields' => array('AggreementForm','Aggreement'),
                    'ValueName' => text('account4'),
                    'ValueDefault' => 12,
                    'PriceRUB' => 0,
                    'PriceUSD' => 0,
                    'PaymentRequired' => true,
                    'Options' => array (
                        array (
                            'OptionId' => 'agile',
                            'Caption' => text('account37'),
                            'PriceRUB' => 4200,
                            'PriceUSD' => 75,
                            'Required' => true
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
                    'CaptionText' => '',
                    'RequiredFields' => array('AggreementForm','Aggreement'),
                    'ValueName' => text('account4'),
                    'ValueDefault' => 12,
                    'PriceRUB' => 0,
                    'PriceUSD' => 0,
                    'PaymentRequired' => true,
                    'Options' => array (
                        array (
                            'OptionId' => 'agile',
                            'Caption' => text('account37'),
                            'PriceRUB' => 18000,
                            'PriceUSD' => 320,
                            'Required' => true
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