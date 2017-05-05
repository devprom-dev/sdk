<?php

class MetricsTypeDictionary extends FieldDictionary
{
    function __construct()
    {
        parent::__construct( getSession()->getProjectIt()->object );
        $this->setNullOption(false);
    }

    function getOptions()
    {
        $options = array();

        $options[] = array (
            'value' => 'N',
            'caption' => text(2297),
            'disabled' => false
        );
        $options[] = array (
            'value' => 'A',
            'caption' => text(2298),
            'disabled' => false
        );
        
        return $options;
    }
}