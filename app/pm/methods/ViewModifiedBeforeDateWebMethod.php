<?php
include_once SERVER_ROOT_PATH."core/methods/FilterDateWebMethod.php";

class ViewModifiedBeforeDateWebMethod extends FilterDateWebMethod
{
    function getCaption()
    {
        return translate('Изменено до');
    }

    function getStyle()
    {
        return 'width:100px;';
    }

    function getValueParm()
    {
        return 'modifiedbefore';
    }
}
