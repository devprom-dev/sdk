<?php

/////////////////////////////////////////////////////////////////////////////////////
class CheckpointSystemAdminEmail extends CheckpointEntryDynamic
{
	function execute()
	{
		$this->setValue( getFactory()->getObject('cms_SystemSettings')->getAll()->get('AdminEmail') != '' ? '1' : '0' );
	}

	function getTitle()
	{
		return text(1873);
	}

	function getDescription()
	{
		return str_replace('%1', '/admin/mailer/', text(1267));
	}

	function getUrl()
	{
		return '/admin/mailer/';
	}

    function getWarning()
    {
        return text(2252);
    }
}
