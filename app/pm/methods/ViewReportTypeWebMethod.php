<?php
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";

class ViewReportTypeWebMethod extends FilterWebMethod
{
    function getCaption()
    {
        return translate('Тип отчета');
    }

    function getValues()
    {
        return array (
            'all' => $this->getCaption().': '.translate('все'),
            'Y' => translate('Пользовательские'),
            'N' => translate('Системные')
        );
    }

    function getStyle()
    {
        return 'width:155px;';
    }

    function getValueParm()
    {
        return 'type';
    }

    function getType()
    {
        return 'singlevalue';
    }
}
