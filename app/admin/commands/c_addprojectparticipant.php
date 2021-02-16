<?php
include_once SERVER_ROOT_PATH.'pm/classes/sessions/PMSession.php';

class AddProjectParticipant extends CommandForm
{
	function validate()
	{
		$this->checkRequired(array('SystemUser', 'Project', 'ProjectRole'));
		return true;
	}

	function create()
	{
	    global $session;

		$project_it = getFactory()->getObject('pm_Project')->getExact($_REQUEST['Project']);
		if ( $project_it->getId() == '' ) {
			$this->replyError( text(200) );
		}

        $session = new PMSession($project_it);
        getFactory()->setAccessPolicy(new AccessPolicy(getFactory()->getCacheService()));

		$baserole_it = getFactory()->getObject('ProjectRole')->getRegistry()->Query(
		    array (
		        new FilterInPredicate($_REQUEST['ProjectRole'])
            )
        );
		if ( $baserole_it->getId() == '' ) {
			$this->replyError( text(200) );
		}

		$userIt = getFactory()->getObject('User')->getExact($_REQUEST['SystemUser']);
		$participant = getFactory()->getObject('pm_Participant');
		
		$part_it = $participant->getByRefArray(array ( 
			'Project' => $_REQUEST['Project'],
			'SystemUser' => $userIt->getId()
		));
		if ( $part_it->count() < 1 )
		{
			$id = $participant->add_parms( array ( 
				'Project' => $_REQUEST['Project'],
				'SystemUser' => $userIt->getId(),
                'NotificationTrackingType' => $userIt->get('NotificationTrackingType'),
                'NotificationEmailType' => $userIt->get('NotificationEmailType')
			));
			$part_it = $participant->getExact($id);
		}

		if ( $part_it->count() < 1 ) $this->replyError( text(706) );
		
		$participant_role = getFactory()->getObject('pm_ParticipantRole');
		$role_it = $participant_role->getByRefArray( array (
			'Project' => $_REQUEST['Project'],
			'Participant' => $part_it->getId(),
			'ProjectRole' => $baserole_it->getId() 
		));
		if ( $role_it->count() < 1 )
		{
		    $objectIt = getFactory()->createEntity($participant_role, array (
                'Project' => $_REQUEST['Project'],
                'Participant' => $part_it->getId(),
                'ProjectRole' => $baserole_it->getId(),
                'Capacity' => $_REQUEST['Capacity'] == '' ? 0 : $_REQUEST['Capacity']
            ));

			if ( $objectIt->getId() < 1 ) $this->replyError( text(706) );
		}
		else {
			$this->replyError( text(627) );
		}

        getFactory()->getCacheService()->invalidate('sessions');
        getFactory()->getCacheService()->invalidate('projects/'.$project_it->get('VPD'));

		$this->replyRedirect( '/admin/users.php', text(665) );
	}
}
