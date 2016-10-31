<?php

class AddProjectParticipant extends CommandForm
{
	function validate()
	{
		$this->checkRequired(array('SystemUser', 'Project', 'ProjectRole'));
		return true;
	}

	function create()
	{
		$project_it = getFactory()->getObject('pm_Project')->getExact($_REQUEST['Project']);
		if ( $project_it->count() < 1 ) {
			$this->replyError( text(200) );
		}

		$baserole_it = getFactory()->getObject('ProjectRole')->getExact( $_REQUEST['ProjectRole'] );
		if ( $baserole_it->count() < 1 ) {
			$this->replyError( text(200) );
		}

		$participant = getFactory()->getObject('pm_Participant');
		
		$part_it = $participant->getByRefArray(array ( 
			'Project' => $_REQUEST['Project'],
			'SystemUser' => $_REQUEST['SystemUser'] 
		));
		if ( $part_it->count() < 1 )
		{
			$id = $participant->add_parms( array ( 
				'Project' => $_REQUEST['Project'],
				'SystemUser' => $_REQUEST['SystemUser'],
				'Notification' => $project_it->getDefaultNotificationType(),
                'VPD' => $project_it->get('VPD')
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
			$mapper = new ModelDataTypeMapper();
			$mapper->map( $participant_role, $_REQUEST );
			
			$id = $participant_role->add_parms( array ( 
				'Project' => $_REQUEST['Project'],
				'Participant' => $part_it->getId(),
				'ProjectRole' => $baserole_it->getId(),
				'Capacity' => $_REQUEST['Capacity'] == '' ? 0 : $_REQUEST['Capacity'],
                'VPD' => $project_it->get('VPD')
			));
			if ( $id < 1 ) $this->replyError( text(706) );
		}
		else {
			$this->replyError( text(627) );
		}

		$this->replyRedirect( '/admin/users.php', text(665) );
	}
}
