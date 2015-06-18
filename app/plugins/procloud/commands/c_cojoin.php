<?php
 
class CoJoin extends CommandForm
{
 	function validate()
 	{
 		global $_REQUEST, $model_factory;
 		
		$this->command = new CreateUser;
 		
		// check for the answer
		if ( $_REQUEST['QHash'] == '' || $_REQUEST['QAnswer'] == '' )
		{
		    return $this->replyError( $this->command->getResultDescription( 1 ) );
		}
		
		$question = $model_factory->getObject('cms_CheckQuestion');
		
		$check_result = $question->checkAnswer( $_REQUEST['QHash'],
			$this->Utf8ToWin($_REQUEST['QAnswer']) );
		
		if ( !$check_result )
		{
			$this->replyError( text('procloud216') );
		}
 		
		$result = $this->command->validate();
	
		if( $result > 0 ) 
		{
			$this->replyError( $this->command->getResultDescription( $result ) );
		}

		return true;
 	}
 	
 	function create()
	{
		global $model_factory;
		
		$user_cls = $model_factory->getObject('cms_User');
		
		$_REQUEST['Login'] = $this->Utf8ToWin($_REQUEST['Login']);
		$_REQUEST['Password'] = $this->Utf8ToWin($_REQUEST['Password']);
		
		$parms['Caption'] = $_REQUEST['Login'];
		$parms['Login'] = $_REQUEST['Login'];
		$parms['Email'] = $_REQUEST['Email'];
		$parms['Password'] = $_REQUEST['Password'];
		$parms['Language'] = '1';
		
		$user_id = $user_cls->add_parms( $parms );
		if ( $user_id > 0 )
		{
			$user_it = $user_cls->getExact($user_id);
			
			$session = getSession();
			$session->open( $user_it );
			
			$this->processUserInvites( $user_it );
			
			$this->replySuccess( $this->getResultDescription( -1 ) );
		}
		else
		{
			$this->replyError( $this->getResultDescription( 9 ) );
		}
	}
	
	function processUserInvites( $user_it )
	{
		global $model_factory, $session;
		
		$invite = $model_factory->getObject('pm_Invitation');
		$invite->disableVpd();
		
		$invite_it = $invite->getByRefArray( array(
			'LCASE(Addressee)' => strtolower($user_it->get('Email'))
		));
		
		while( !$invite_it->end() )
		{
			$session = new PMSession($invite_it->getRef('Project'));
			
			getFactory()->setAccessPolicy( new AccessPolicy() );
			
			$session->open( $user_it );
			
			$participant = $model_factory->getObject('pm_Participant');
			
			$part_it = $participant->getByRefArray( array (
				'SystemUser' => $user_it->getId(),
				'Project' => $invite_it->get('Project')
			));
			
			if ( $part_it->count() < 1 )
			{
				$part_id = $participant->add_parms( array (
					'SystemUser' => $user_it->getId(),
					'IsActive' => 'Y',
					'Project' => $invite_it->get('Project'),
					'Notification' => 'every1hour'
				));
				
				if ( $part_id < 1 )
				{
					$this->replyError( 'Невозможно включить пользователя в проект по приглашению' );
				}
			}
			else
			{
				$part_id = $part_it->getId();
			}

			$role = $model_factory->getObject('pm_ProjectRole');
			
			$role->addSort( new SortAttributeClause('ReferenceName') );
			
			$role_it = $role->getByRefArray( array (
				'ReferenceName' => array( 'developer', 'lead' )
			));

			if ( $role_it->getId() < 1 )
			{
				$this->replyError( 'Не найдена проектная роль для нового участника, добавленного по приглашению' );
			}
			
			$partrole = $model_factory->getObject('pm_ParticipantRole');
			
			$part_role_it = $partrole->getByRefArray( array (
					'Participant' => $part_id,
					'ProjectRole' => $role_it->getId()
			));
			
			if ( $part_role_it->count() < 1 )
			{
				$role_id = $partrole->add_parms( array (
					'Participant' => $part_id,
					'ProjectRole' => $role_it->getId(),
					'Capacity' => 1
				));
	
				if ( $role_id < 1 )
				{
					$this->replyError( 'Невозможно участнику проекта назначить роль' );
				}
			}
			else
			{
				$role_id = $part_role_it->getId();
			}
			
			$invite_it->delete();
			
			$session->drop();
			
			$invite_it->moveNext();
		}
	}
	
	function getResultDescription( $result )
	{
		global $_REQUEST;
		
		if ( $result == -1 && $_REQUEST['skip'] == 'info' )
		{
			return ' > ';
		}
		else
		{
			return $this->command->getResultDescription( $result );
		}
	}
}
