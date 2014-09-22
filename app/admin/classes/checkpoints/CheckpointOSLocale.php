<?php

class CheckpointOSLocale extends CheckpointEntryDynamic
{
    function execute()
    {
        if ( function_exists('mb_strtolower') )
        {
            $this->setValue( mb_strtolower("Регистр") == "регистр" ? "1" : "0" );
        }
        else
        {
            $this->setValue( strtolower("Регистр") == "регистр" ? "1" : "0" );
        }
    }

    function getTitle()
    {
        return text(1147);
    }

    function getDescription()
    {
        return text(1148);
    }
}
