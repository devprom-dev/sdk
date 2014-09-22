<?php

include_once "DevpromPMApplicationTestCase.php";

class DevpromDummyTestCase extends DevpromPMApplicationTestCase
{
    function getMethodologyIt()
    {
        $methodology = new Methodology();
        
        return $methodology->createCachedIterator(array(
             array( 'pm_MethodologyId' => '1', 'IsReportsOnActivities' => 'N', 'IsPlanningUsed' => 'N' )
        ));
    }
}
