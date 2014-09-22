<?php

include "CalendarRegistry.php";

class Calendar extends Metaobject
{
    function __construct()
    {
        parent::__construct('pm_CalendarInterval', new CalendarRegistry($this));
    }
}