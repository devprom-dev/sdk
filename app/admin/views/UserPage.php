<?php

include_once SERVER_ROOT_PATH."admin/classes/UserModelExtendedBuilder.php";

include ('UserForm.php');
include ('UserTable.php');
include ('UserFormAsyncParticipancePre.php');
include ('UserFormAsyncParticipance.php');

class UserPage extends AdminPage
{
	var $project_it;

	function __construct()
	{
		parent::Page();

		$object_it = $this->getObjectIt();

		if ( $this->needDisplayForm() && is_object($object_it) && $object_it->getId() > 0 )
		{
			$this->addInfoSection( new LastChangesSection( $object_it ) );
		}
	}
	
	function getObject()
	{
		getSession()->addBuilder( new UserModelExtendedBuilder() );
		
	    return getFactory()->getObject('User');
	}

	function getTable()
	{
		return new UserTable( $this->getObject() );
	}

	function getForm()
	{
		global $_REQUEST;

		if ( $_REQUEST['cms_UserId'] != '' )
		{
			$user_it = $this->getObject()->getExact($_REQUEST['cms_UserId']);
		}
		
		if ( is_object($user_it) && $_REQUEST['mode'] == 'role' )
		{
			return new UserParticipanceForm( $user_it );
		}
		
		if ( is_object($user_it) && $_REQUEST['mode'] == 'participant' )
		{
			return new UserParticipancePreForm( $user_it );
		}

		return new UserForm( $this->getObject() );
	}
}
