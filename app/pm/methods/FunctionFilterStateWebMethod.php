<?php

class FunctionFilterStateWebMethod extends FilterWebMethod
{
    function getCaption()
    {
        return translate('Состояние');
    }

    function getValues()
    {
        return array (
            'all' => translate('Все'),
            'open' => translate('Не реализованы'),
            'closed'  => translate('Реализованы')
        );
    }

    function getStyle()
    {
        return 'width:125px;';
    }

    function getValueParm()
    {
        return 'state';
    }

    function getValue()
    {
        $value = parent::getValue();

        if ( $value == '' )
        {
            return 'all';
        }

        return $value;
    }

    function getType()
    {
        return 'singlevalue';
    }
}