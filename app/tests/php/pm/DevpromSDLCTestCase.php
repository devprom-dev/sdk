<?php

include_once "DevpromPMApplicationTestCase.php";

class DevpromSDLCTestCase extends DevpromPMApplicationTestCase
{
    function getMethodologyIt()
    {
        $methodology = new Methodology();
        
        return $methodology->createCachedIterator(array(
             array( 
                     'pm_MethodologyId' => '1', 
                     'IsReportsOnActivities' => 'Y', 
                     'IsPlanningUsed' => 'Y', 
                     'IsReleasesUsed' => 'I', 
             		 'IsVersionsUsed' => 'Y',
             		 'HasMilestones' => 'Y',
             		 'IsTasks' => 'Y'
                  )
        ));
    }
}
