<?php

include_once 'CheckpointEntryBase.php';

class CheckpointEntryDynamic extends CheckpointEntryBase
{
    var $value = '';

    function setValue( $value )
    {
        $this->value = $value;
    }

    function getValue()
    {
        return $this->value;
    }

    function check()
    {
        return $this->getValue() == '' || $this->getValue();
    }

    function execute()
    {
    }
}
