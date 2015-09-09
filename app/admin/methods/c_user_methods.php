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
		if ( !$this->hasAccess() ) return;

		$list = new Metaobject('cms_BlackList');
		$list_it = $list->getByRef('SystemUser', $parms['user']);
		while( !$list_it->end() ) {
			$list->delete($list_it->getId());
			$list_it->moveNext();
		}

		$list = new Metaobject('cms_LoginRetry');
		$list_it = $list->getByRef('SystemUser', $parms['user']);

		while( !$list_it->end() ) {
			$list->delete($list_it->getId());
			$list_it->moveNext();
		}
	}

	function hasAccess()
	{
		return getSession()->getUserIt()->IsAdministrator()
			&& getFactory()->getAccessPolicy()->can_create(getFactory()->getObject('User')); 
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