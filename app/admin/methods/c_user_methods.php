<?php

include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";
include_once SERVER_ROOT_PATH.'pm/classes/sessions/PMSession.php';

///////////////////////////////////////////////////////////////////////////////////////
class UserWebMethod extends WebMethod
{
	function execute_request()
	{
		global $_REQUEST;
		$this->execute( $_REQUEST );
	}
}

///////////////////////////////////////////////////////////////////////////////////////
class BlockUserWebMethod extends UserWebMethod
{
	function getCaption() {
		return translate('Заблокировать');
	}

	function execute( $parms )
	{
		if ( $this->hasAccess() )
		{
			$list = new Metaobject('cms_BlackList');
			$list_it = $list->getByRef('SystemUser',
			$parms['user']);
				
			if ( $list_it->count() < 1 )
			{
				$list->add_parms(
				array (
	 					'SystemUser' => $parms['user'],
	 					'IPAddress' => '-',
	 					'BlockReason' => translate('Пользователь заблокирован администратором')
				)
				);
			}
				
			echo '/admin/blacklist.php';
		}
	}

	function hasAccess()
	{
		return getSession()->getUserIt()->IsAdministrator();
	}
}

///////////////////////////////////////////////////////////////////////////////////////
class UnBlockUserWebMethod extends UserWebMethod
{
	function getCaption() {
		return translate('Разблокировать');
	}

	function execute( $parms )
	{
		if ( $this->hasAccess() )
		{
			$list = new Metaobject('cms_BlackList');
			$list_it = $list->getByRef('SystemUser',
			$parms['user']);
				
			if ( $list_it->count() > 0 )
			{
				$list->delete($list_it->getId());
			}

			echo '/admin/blacklist.php';
		}
	}

	function hasAccess()
	{
		return getSession()->getUserIt()->IsAdministrator()
			&& getFactory()->getAccessPolicy()->can_create(getFactory()->getObject('User')); 
	}
}

///////////////////////////////////////////////////////////////////////////////////////
class UserRelateToProjectWebMethod extends UserWebMethod
{
	var $user_it;

	function UserRelateToProjectWebMethod ( $user_it = null )
	{
		$this->user_it = $user_it;
	}

	function getCaption()
	{
		return translate('Включить в проект');
	}

	function getRedirectUrl()
	{
		return $this->user_it->getViewUrl().'&mode=participant';
	}

	function execute( $parms )
	{
		global $model_factory;
			
		$user = $model_factory->getObject('cms_User');
		
		$this->user_it = $user->getExact($parms['user']);
			
		if ( $this->user_it->count() > 0 )
		{
			echo $this->getRedirectUrl();
		}
	}

	function hasAccess()
	{
		return getSession()->getUserIt()->IsAdministrator();
	}
}

///////////////////////////////////////////////////////////////////////////////////////
class UserExcludeWebMethod extends UserWebMethod
{
	var $user_it, $project_it;

	function UserExcludeWebMethod ( $user_it = null, $project_it = null )
	{
		$this->user_it = $user_it;
		$this->project_it = $project_it;
		
		parent::WebMethod();
	}

	function getCaption()
	{
		return text(1248);
	}
	
	function getJSCall()
	{
		return parent::getJSCall( array(
			'user' => $this->user_it->getId(),
			'project' => $this->project_it->getId()
		));
	}
	
	function execute( $parms )
	{
		$this->user_it = getFactory()->getObject('User')->getExact($parms['user']);
		
		if ( $this->user_it->getId() < 1 ) throw new Exception('User should be specified');
		
		$this->project_it = getFactory()->getObject('Project')->getExact($parms['project']);
		
		if ( $this->project_it->getId() < 1 ) throw new Exception('Project should be specified');

		$session = new PMSession($this->project_it);
		
		getFactory()->setAccessPolicy(new AccessPolicy(getFactory()->getCacheService()));
		
		$part_it = getFactory()->getObject('Participant')->getByRefArray( 
				array (
					'SystemUser' => $this->user_it->getId(),
					'Project' => $this->project_it->getId() 	
				)
			);

		$part_it->modify( 
				array ( 
						'IsActive' => 'N' 
				)
		);
		
		$role_it = getFactory()->getObject('pm_ParticipantRole')->getByRef( 'Participant', $part_it->getId() );

		while( !$role_it->end() )
		{
		    $role_it->delete();
		    
		    $role_it->moveNext();
		}
	}

	function hasAccess()
	{
		return getSession()->getUserIt()->IsAdministrator();
	}
}

///////////////////////////////////////////////////////////////////////////////////////
class UserFilterRoleWebMethod extends FilterWebMethod
{
	function getCaption()
	{
		return translate('Роль');
	}

	function getValues()
	{
		global $model_factory;
			
		$values = array (
 			'all' => translate('Все'),
 			'admin' => translate('Администратор')
		);

		return $values;
	}

	function getStyle()
	{
		return 'width:120px;';
	}

	function getValueParm()
	{
		return 'role';
	}
	
 	function getType()
 	{
 		return 'singlevalue';
 	}
}

///////////////////////////////////////////////////////////////////////////////////////
class UserFilterStateWebMethod extends FilterWebMethod
{
	function getCaption()
	{
		return translate('Состояние');
	}

	function getValues()
	{
		global $model_factory;
			
		$values = array (
 			'active' => translate('Активны'),
 			'blocked' => translate('Заблокированы')
		);

		return $values;
	}

	function getStyle()
	{
		return 'width:120px;';
	}

	function getValueParm()
	{
		return 'state';
	}

	function getValue()
	{
		$value = parent::getValue();

		if ( $value == '' )
		{
			return 'active';
		}

		return $value;
	}
}

?>