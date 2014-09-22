<?php

class SetupCalendar extends Installable 
{
    function check()
    {
        return true;
    }

    function install()
    {
        getFactory()->getObject('Calendar')->getAll();
        
        return true;
    }
}
