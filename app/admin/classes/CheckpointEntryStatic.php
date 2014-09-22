<?php

include_once 'CheckpointEntryBase.php';

class CheckpointEntryStatic extends CheckpointEntryBase
{
    function check()
    {
        return $this->getValue();
    }
}
