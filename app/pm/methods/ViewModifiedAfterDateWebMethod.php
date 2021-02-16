<?php
include_once SERVER_ROOT_PATH."core/methods/FilterDateWebMethod.php";

class ViewModifiedAfterDateWebMethod extends FilterDateWebMethod
{
    function __construct() {
        parent::__construct();
        $this->setCaption(translate('Изменено после'));
    }

    function getStyle()
    {
        return 'width:100px;';
    }

    function getValueParm()
    {
        return 'modifiedafter';
    }
}
