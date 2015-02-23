<?php

include ('SystemCheckList.php');

class SystemCheckTable extends StaticPageTable
{
	function getList()
	{
		return new SystemCheckList( $this->getObject() );
	}

	function getFilterActions()
	{
		return array();
	}
	
	function getCaption()
	{
	    return '';
	}
	
	function getSortFields()
	{
		return array('Caption');
	}
	
	function getActions()
	{
	    $job_it = getFactory()->getObject('co_ScheduledJob')->getByRef('ClassName', 'processcheckpoints');
	    
	    return array(
	            array ( 'name' => text(1380), 'url' => '/tasks/command.php?class=runjobs&job='.$job_it->getId().'&redirect=/admin/checks.php' )
	    );
	}
}
