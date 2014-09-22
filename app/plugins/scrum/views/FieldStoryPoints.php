<?php

include_once SERVER_ROOT_PATH."cms/views/FieldDictionary.php";

class FieldStoryPoints extends FieldDictionary
{
    var $scale;
    
    function __construct( $object, $scale )
    {
        $this->scale = $scale;
        
        parent::__construct( $object );
    }
    
    function getOptions()
    {
        return $this->scale;
    }
}
