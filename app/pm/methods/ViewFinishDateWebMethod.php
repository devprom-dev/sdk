<?php
include_once SERVER_ROOT_PATH."core/methods/FilterDateWebMethod.php";

class ViewFinishDateWebMethod extends FilterDateWebMethod
{
    function __construct( $title = 'Окончание' )
    {
        parent::__construct();
        $this->setCaption(translate($title));
    }

    function getStyle() {
        return 'width:100px;';
    }

    function getValueParm() {
        return 'finish';
    }
}
