<?php

/////////////////////////////////////////////////////////////////////////////////////
class CheckpointSystemAdminEmail extends CheckpointEntryDynamic
{
	function execute()
	{
		global $model_factory;
		
		$system = $model_factory->getObject('cms_SystemSettings');

		$system_it = $system->getAll();

		$this->setValue( $system_it->get('AdminEmail') != '' ? '1' : '0' );
	}

	function getTitle()
	{
		return 'Email notifications';
	}

	function getDescription()
	{
		return str_replace('%1', '/admin/mailer/', text(1267));
	}
}
