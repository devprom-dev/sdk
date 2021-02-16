<?php
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";

class UserFilterStateWebMethod extends FilterWebMethod
{
    function getCaption()
    {
        return translate('Состояние');
    }

    function getType()
    {
        return 'singlevalue';
    }

    function getValues()
    {
        $values = array (
            'nonblocked' => translate('Активны'),
            'blocked' => translate('Заблокированы')
        );

        return $values;
    }

    function getStyle()
    {
        return 'width:120px;';
    }

    function getValueParm()
    {
        return 'state';
    }

    function getValue()
    {
        $value = parent::getValue();
        if ( $value == '' ) return 'nonblocked';
        return $value;
    }
}