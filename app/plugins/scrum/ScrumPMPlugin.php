<?php

include "classes/widgets/FunctionalAreaMenuScrumBuilder.php";
include "views/EstimationStrategyScrumBuilder.php";
include "classes/VelocityReportsBuilder.php";
include "classes/events/ScrumReportedEvent.php";
include "classes/notificators/ScrumChangeNotificator.php";
include "classes/predicates/ProjectScrumPredicate.php";
include "classes/widgets/ScrumTourScriptBuilder.php";

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
		$modules = array (
			'velocitychart' => array(
				'includes' => array( 'scrum/views/VelocityPage.php' ),
				'classname' => 'VelocityPage',
				'title' => text('scrum9'),
				'AccessEntityReferenceName' => 'pm_Version',
				'area' => FUNC_AREA_MANAGEMENT,
				'icon' => 'icon-signal'
			)
		);

        if ( !$this->checkEnabled() ) return $modules;
        	
        $modules['meetings'] = array(
			'includes' => array( 'scrum/views/ScrumPage.php' ),
			'classname' => 'ScrumPage',
			'title' => text('scrum1'),
			'description' => text('scrum18'),
			'AccessEntityReferenceName' => 'pm_Scrum',
			'area' => FUNC_AREA_MANAGEMENT
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
                new ScrumReportedEvent(),
				new ScrumTourScriptBuilder(getSession())
        );
    }

 	function getObjectAccess( $action, $role_ref_name, & $object_it )
 	{
 		if ( $object_it->object instanceof Scrum ) {
 			return $action == ACCESS_READ || $object_it->get('Participant') == getSession()->getParticipantIt()->getId();
 		}
 	}

	function getObjectActions( $object_it )
	{
		if ( $object_it->object instanceof Request ) {
			return $this->getIssueActions($object_it);
		}
		return array();
	}

	protected function getIssueActions( $object_it )
	{
		if ( !is_object($this->method_toepic) )
		{
			if ( is_null($this->scrum_vpds) ) {
                $registry = getFactory()->getObject('Project')->getRegistry();
                $registry->setPersisters(array());
                $this->scrum_vpds = $registry->Query(
						array(new ProjectScrumPredicate())
					)->fieldToArray('VPD');
			}
			$this->method_toepic = new ObjectCreateNewWebMethod(getFactory()->getObject('Feature'));
		}

		if ( !in_array($object_it->get('VPD'), $this->scrum_vpds) || $object_it->IsFinished() ) return array();
		$this->method_toepic->setVpd($object_it->get('VPD'));
		return array (
				array (
						'name' => text('scrum19'),
						'url' => $this->method_toepic->getJSCall(array(
										'Request' => $object_it->getId()
									))
				)
			);
	}

	private $method_toepic = null;
	private $scrum_vpds = null;
}