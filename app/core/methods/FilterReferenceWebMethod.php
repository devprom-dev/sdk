<?php
include_once "WebMethod.php";

class FilterReferenceWebMethod extends WebMethod
{
    private $object = null;

    function __construct( $object = null, $title = '', $parmvalue = '')
    {
        $this->object = $object;
        $this->setCaption($title != '' ? $title : $this->object->getDisplayName());
        $this->setValueParm($parmvalue != '' ? $parmvalue : strtolower(get_class($this->object)));
    }

    function getValue() {
        return $this->object->getExact(parent::getValue())->getDisplayName();
    }
}
