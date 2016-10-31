<?php
include_once SERVER_ROOT_PATH."pm/views/ui/FieldCustomScaleDictionary.php";

class FieldEstimationDictionary extends FieldCustomScaleDictionary
{
    function __construct( $object )
    {
        $options = array();
        $scale = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy()->getScale();
        if ( array_keys($scale) !== range(0, count($scale) - 1) ) {
            foreach( $scale as $key => $value ) {
                $options[] = array (
                    'value' => $value,
                    'caption' => $key
                );
            }
        }
        parent::__construct( $object, $options );
    }
}
