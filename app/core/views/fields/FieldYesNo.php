<?php

class FieldYesNo extends FieldDictionary
{
    function __construct() {
        parent::__construct(getFactory()->getObject('entity'));
    }

    function getOptions()
    {
        return array(
            array(
                'value' => 'Y',
                'referenceName' => 'Y',
                'caption' => translate('Да'),
                'disabled' => false
            ),
            array(
                'value' => 'N',
                'referenceName' => 'N',
                'caption' => translate('Нет'),
                'disabled' => false
            )
        );
    }
}