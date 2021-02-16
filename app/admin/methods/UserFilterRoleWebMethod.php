<?php
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";

class UserFilterRoleWebMethod extends FilterWebMethod
{
    function getCaption()
    {
        return translate('Категория');
    }

    function getValues()
    {
        $values = array (
            'all' => translate('Все'),
            'admin' => translate('Администратор')
        );

        return $values;
    }

    function getStyle()
    {
        return 'width:120px;';
    }

    function getValueParm()
    {
        return 'role';
    }

    function getType()
    {
        return 'singlevalue';
    }
}