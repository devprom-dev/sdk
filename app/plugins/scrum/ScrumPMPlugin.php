<?php

include "classes/widgets/FunctionalAreaMenuScrumBuilder.php";
include "views/EstimationStrategyScrumBuilder.php";
include "classes/VelocityReportsBuilder.php";
include "classes/notificators/EmailScrumNotificator.php";
include "classes/notificators/ScrumChangeNotificator.php";

class ScrumPMPlugin extends PluginPMBase
{
    var $enabled;
    
 	function checkEnabled()
 	{
 	    if ( isset($this->enabled) ) return $this->enabled;

 	    if ( !is_a(getSession(), 'PMSession') ) return false;
 	    
 		$this->enabled = getSession()->getProjectIt()->getMethodologyIt()->get('UseScrums') == 'Y';
 		
 		return $this->enabled;
    }

    function getModules()
    {
        if ( !$this->checkEnabled() ) return array();
        	
        $modules = array (
                'meetings' => array(
                    'includes' => array( 'scrum/views/ScrumPage.php' ),
                    'classname' => 'ScrumPage',
                    'title' => text('scrum1'),
                	'description' => text('scrum18'),
                    'AccessEntityReferenceName' => 'pm_Scrum',
                	'area' => FUNC_AREA_MANAGEMENT
                ),
                'velocitychart' => array(
                    'includes' => array( 'scrum/views/VelocityPage.php' ),
                    'classname' => 'VelocityPage',
                    'title' => text('scrum9'),
                    'AccessEntityReferenceName' => 'pm_Version',
                	'area' => FUNC_AREA_MANAGEMENT
                )
        );

        return $modules;
    }

    function getBuilders()
    {
        return array (
                new FunctionalAreaMenuScrumBuilder(),
                new VelocityReportsBuilder(),
                new EstimationStrategyScrumBuilder(),
                new ScrumChangeNotificator(),
                new EmailScrumNotificator()
        );
    }

 	function getObjectAccess( $action, $role_ref_name, & $object_it )
 	{
 	    switch ( $object_it->object->getEntityRefName() )
 	    {
 	        case 'pm_Scrum':
 	            
				return $action == ACCESS_READ || $object_it->get('Participant') == getSession()->getParticipantIt()->getId();
 	    }
 	}
}