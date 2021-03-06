<?php

class CheckpointPhpVersion extends CheckpointEntryStatic
{
    function getRequiredValue()
    {
        return '5.5.0';
    }

    function getValue()
    {
        return version_compare(phpversion(), $this->getRequiredValue(), '>=') == 1 ? '1' : '0';
    }

    function check()
    {
        return $this->getValue();
    }

    function getRequired()
    {
        return true;
    }
    
    function getTitle()
    {
        return 'PHP: Version';
    }

    function getDescription()
    {
        return str_replace('%1', $this->getRequiredValue(), text(1244));
    }
}
