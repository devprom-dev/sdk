<?php

include_once "DevpromPMApplicationTestCase.php";

class DevpromKanbanTestCase extends DevpromPMApplicationTestCase
{
    function getMethodologyIt()
    {
        $methodology = new Methodology();
        
        return $methodology->createCachedIterator(array(
             array( 
                     'pm_MethodologyId' => '1', 
                     'IsReportsOnActivities' => 'Y', 
                     'IsPlanningUsed' => 'N', 
                     'IsReleasesUsed' => 'N' 
                  )
        ));
    }
}
