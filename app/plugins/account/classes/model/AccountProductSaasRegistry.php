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
                    'ValueDefault' => 3,
                    'PaymentRequired' => true,
                    'PriceRUB' => 0,
                    'PriceUSD' => 0,
                    'Options' => array (
                        array (
                            'OptionId' => 'agile',
                            'Caption' => text('account37'),
                            'PriceRUB' => 1800,
                            'PriceUSD' => 1800,
                            'Required' => true
                        ),
                        array (
                            'OptionId' => 'support',
                            'Caption' => text('account30'),
                            'PriceRUB' => 1200,
                            'PriceUSD' => 1200
                        ),
                        array (
                            'OptionId' => 'reqs',
                            'Caption' => text('account31'),
                            'PriceRUB' => 1500,
                            'PriceUSD' => 1500
                        ),
                        array (
                            'OptionId' => 'qa',
                            'Caption' => text('account32'),
                            'PriceRUB' => 1200,
                            'PriceUSD' => 1200
                        ),
                        array (
                            'OptionId' => 'docs',
                            'Caption' => text('account33'),
                            'PriceRUB' => 1000,
                            'PriceUSD' => 1000
                        )
                    )
        		),
				array (
                    'entityId' => 'LicenseSAASALM20',
                    'Caption' => text('account42'),
                    'CaptionText' => '',
                    'RequiredFields' => array('AggreementForm','Aggreement'),
                    'ValueName' => text('account4'),
                    'ValueDefault' => 3,
                    'PriceRUB' => 0,
                    'PriceUSD' => 0,
                    'PaymentRequired' => true,
                    'Options' => array (
                            array (
                                'OptionId' => 'agile',
                                'Caption' => text('account37'),
                                'PriceRUB' => 5400,
                                'PriceUSD' => 5400,
                                'Required' => true
                            ),
                            array (
                                    'OptionId' => 'support',
                                    'Caption' => text('account30'),
                                    'PriceRUB' => 1200,
                                    'PriceUSD' => 1200
                            ),
                            array (
                                    'OptionId' => 'reqs',
                                    'Caption' => text('account31'),
                                    'PriceRUB' => 4500,
                                    'PriceUSD' => 4500
                            ),
                            array (
                                    'OptionId' => 'qa',
                                    'Caption' => text('account32'),
                                    'PriceRUB' => 3600,
                                    'PriceUSD' => 3600
                            ),
                            array (
                                    'OptionId' => 'docs',
                                    'Caption' => text('account33'),
                                    'PriceRUB' => 3000,
                                    'PriceUSD' => 3000
                            )
                    )
				),
        		array (
                    'entityId' => 'LicenseSAASALMMiddle',
                    'Caption' => text('account11'),
                    'CaptionText' => '',
                    'RequiredFields' => array('AggreementForm','Aggreement'),
                    'ValueName' => text('account4'),
                    'ValueDefault' => 3,
                    'PriceRUB' => 0,
                    'PriceUSD' => 0,
                    'PaymentRequired' => true,
                    'Options' => array (
                        array (
                            'OptionId' => 'agile',
                            'Caption' => text('account37'),
                            'PriceRUB' => 7200,
                            'PriceUSD' => 7200,
                            'Required' => true
                        ),
                        array (
                            'OptionId' => 'support',
                            'Caption' => text('account30'),
                            'PriceRUB' => 1200,
                            'PriceUSD' => 1200
                        ),
                        array (
                            'OptionId' => 'reqs',
                            'Caption' => text('account31'),
                            'PriceRUB' => 6000,
                            'PriceUSD' => 6000
                        ),
                        array (
                            'OptionId' => 'qa',
                            'Caption' => text('account32'),
                            'PriceRUB' => 4800,
                            'PriceUSD' => 4800
                        ),
                        array (
                            'OptionId' => 'docs',
                            'Caption' => text('account33'),
                            'PriceRUB' => 4000,
                            'PriceUSD' => 4000
                        )
                    )
        		)
        ));
    }
}