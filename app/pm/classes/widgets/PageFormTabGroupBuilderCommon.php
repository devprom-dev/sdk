<?php
include_once "PageFormTabGroupBuilder.php";

class PageFormTabGroupBuilderCommon extends PageFormTabGroupBuilder
{
    function build( PageFormTabGroupRegistry $registry )
    {
        $registry->add('deadlines', translate('Сроки'));
        $registry->add('additional', translate('Дополнительно'));
        $registry->add('trace,source-attribute', translate('Трассировки'));
    }
}