<?php
include_once SERVER_ROOT_PATH."cms/views/FieldDictionary.php";

class FieldMergeStrategyDictionary extends FieldDictionary
{
    function __construct() {
        parent::__construct( new Metaobject('entity') );
    }

    function getOptions()
    {
        return array(
            array (
                'value' => 1,
                'referenceName' => 1,
                'caption' => text(2818),
                'disabled' => false
            ),
            array (
                'value' => 2,
                'referenceName' => 2,
                'caption' => text(2817),
                'disabled' => false
            )
        );
    }
}
