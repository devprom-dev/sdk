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
                            'PriceUSD' => 800,
                            'Required' => true
                        ),
                        array (
                            'OptionId' => 'support',
                            'Caption' => text('account30'),
                            'PriceRUB' => 600,
                            'PriceUSD' => 600
                        ),
                        array (
                            'OptionId' => 'reqs',
                            'Caption' => text('account31'),
                            'PriceRUB' => 600,
                            'PriceUSD' => 600
                        ),
                        array (
                            'OptionId' => 'qa',
                            'Caption' => text('account32'),
                            'PriceRUB' => 500,
                            'PriceUSD' => 500
                        ),
                        array (
                            'OptionId' => 'docs',
                            'Caption' => text('account33'),
                            'PriceRUB' => 500,
                            'PriceUSD' => 500
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
                                'PriceUSD' => 2500,
                                'Required' => true
                            ),
                            array (
                                    'OptionId' => 'support',
                                    'Caption' => text('account30'),
                                    'PriceRUB' => 1900,
                                    'PriceUSD' => 1900
                            ),
                            array (
                                    'OptionId' => 'reqs',
                                    'Caption' => text('account31'),
                                    'PriceRUB' => 1900,
                                    'PriceUSD' => 1900
                            ),
                            array (
                                    'OptionId' => 'qa',
                                    'Caption' => text('account32'),
                                    'PriceRUB' => 1550,
                                    'PriceUSD' => 1550
                            ),
                            array (
                                    'OptionId' => 'docs',
                                    'Caption' => text('account33'),
                                    'PriceRUB' => 1550,
                                    'PriceUSD' => 1550
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
                            'PriceUSD' => 4200,
                            'Required' => true
                        ),
                        array (
                            'OptionId' => 'support',
                            'Caption' => text('account30'),
                            'PriceRUB' => 3200,
                            'PriceUSD' => 3200
                        ),
                        array (
                            'OptionId' => 'reqs',
                            'Caption' => text('account31'),
                            'PriceRUB' => 3200,
                            'PriceUSD' => 3200
                        ),
                        array (
                            'OptionId' => 'qa',
                            'Caption' => text('account32'),
                            'PriceRUB' => 2600,
                            'PriceUSD' => 2600
                        ),
                        array (
                            'OptionId' => 'docs',
                            'Caption' => text('account33'),
                            'PriceRUB' => 2600,
                            'PriceUSD' => 2600
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
                            'PriceUSD' => 18000,
                            'Required' => true
                        ),
                        array (
                            'OptionId' => 'support',
                            'Caption' => text('account30'),
                            'PriceRUB' => 13000,
                            'PriceUSD' => 13000
                        ),
                        array (
                            'OptionId' => 'reqs',
                            'Caption' => text('account31'),
                            'PriceRUB' => 13000,
                            'PriceUSD' => 13000
                        ),
                        array (
                            'OptionId' => 'qa',
                            'Caption' => text('account32'),
                            'PriceRUB' => 11000,
                            'PriceUSD' => 11000
                        ),
                        array (
                            'OptionId' => 'docs',
                            'Caption' => text('account33'),
                            'PriceRUB' => 11000,
                            'PriceUSD' => 11000
                        )
                    )
        		)
        ));
    }
}