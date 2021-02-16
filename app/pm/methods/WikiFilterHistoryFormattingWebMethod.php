<?php
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";

class WikiFilterHistoryFormattingWebMethod extends FilterWebMethod
{
    function getCaption()
    {
        return translate('Форматирование');
    }

    function getValues()
    {
        return array (
            'text' => translate('Только текст'),
            'full' => translate('Текст и стили')
        );
    }

    function getStyle()
    {
        return 'width:110px;';
    }

    function getValueParm()
    {
        return 'formatting';
    }

    function getValue()
    {
        $value = parent::getValue();

        if ( $value == '' )
        {
            return 'text';
        }

        return $value;
    }

    function getType()
    {
        return 'singlevalue';
    }
}

