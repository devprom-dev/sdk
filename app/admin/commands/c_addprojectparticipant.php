<?php

include_once SERVER_ROOT_PATH.'pm/classes/sessions/PMSession.php';

class AddProjectParticipant extends CommandForm
{
	function validate()
	{
		global $_REQUEST, $model_factory;

		$this->checkRequired(
		array('SystemUser', 'Project', 'ProjectRole') );

		return true;
	}

	function create()
	{
		global $session;

		$project_it = getFactory()->getObject('pm_Project')->getExact($_REQUEST['Project']);
		if ( $project_it->count() < 1 )
		{
			$this->replyError( text(200) );
		}

		$session = new PMSession($project_it);
		
		$baserole_it = getFactory()->getObject('ProjectRole')->getExact( $_REQUEST['ProjectRole'] );
		if ( $baserole_it->count() < 1 )
		{
			$this->replyError( text(200) );
		}

		// check for participant exists
		//
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
				'Notification' => $project_it->getDefaultNotificationType() 
			));

			$part_it = $participant->getExact($id);
		}

		if ( $part_it->count() < 1 ) $this->replyError( text(706) );
		
		// create participant role
		//
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
				'Capacity' => $_REQUEST['Capacity'] == '' ? 0 : $_REQUEST['Capacity'] 
			));
			
			if ( $id < 1 )
			{
				$this->replyError( text(706) );
			}
		}
		else
		{
			$this->replyError( text(627) );
		}

		// report result of the operation
		//
		$this->replyRedirect( '/admin/users.php', text(665) );
	}
}
