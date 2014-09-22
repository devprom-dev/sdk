<?php

///////////////////////////////////////////////////////////////////////////////////////
class ProjectWebMethod extends WebMethod
{
	function execute_request()
	{
		global $_REQUEST;
		$this->execute( $_REQUEST );
	}
}

///////////////////////////////////////////////////////////////////////////////////////
class ProjectDeleteWebMethod extends ProjectWebMethod
{
	var $object_it;

	function ProjectDeleteWebMethod( $object_it = null )
	{
		$this->object_it = $object_it;
			
		parent::WebMethod();
	}

	function getCaption()
	{
		return translate('Удалить');
	}

	function getJSCall( $object )
	{
	    return "javascript: bulkDelete('".strtolower(get_class($object))."', '".get_class($this)."', '".$this->getRedirectUrl()."'); ";
	}
	
	function getWarning()
	{
		return text(636);
	}

	function hasAccess()
	{
		return getSession()->getUserIt()->IsAdministrator();
	}

	function getBackupUrl()
	{
	    return '/admin/backups.php?action=backupdatabase&parms=project,';
	}
	
	function getRedirectUrl()
	{
		return $this->getBackupUrl().(is_object($this->object_it) ? $this->object_it->getId() : '');
	}

	function execute_request()
	{
	    if ( $_REQUEST['class'] == '' || $_REQUEST['objects'] == '' ) return;
	    
	    echo $_REQUEST['objects'];
	}
}
