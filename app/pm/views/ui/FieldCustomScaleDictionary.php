<?php
include_once SERVER_ROOT_PATH."cms/views/FieldDictionary.php";

class FieldCustomScaleDictionary extends FieldDictionary
{
    function __construct( $object, $scale )
    {
        $this->scale = $scale;
        parent::__construct( $object );
    }
    
    function getOptions() {
        return $this->scale;
    }

    private $scale;
}
