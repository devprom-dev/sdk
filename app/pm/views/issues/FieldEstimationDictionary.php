<?php
include_once SERVER_ROOT_PATH."pm/views/ui/FieldCustomScaleDictionary.php";

class FieldEstimationDictionary extends FieldCustomScaleDictionary
{
    function __construct( $object )
    {
        $options = array();
        $strategy = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy();
        $scale = $strategy->getScale();
        if ( array_keys($scale) !== range(0, count($scale) - 1) ) {
            foreach( $scale as $key => $value ) {
                $options[] = array (
                    'value' => $value,
                    'caption' => $strategy->getDimensionText($key)
                );
            }
        }
        parent::__construct( $object, $options );
    }
}
