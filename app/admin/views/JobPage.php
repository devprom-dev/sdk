<?php

include ('JobForm.php');
include ('JobTable.php');
include ('JobRunTable.php');
include ('JobPageSectionDescription.php');

class JobPage extends AdminPage
{
	var $project_it, $job_it;

	function JobPage()
	{
		global $_REQUEST, $model_factory;
			
		if ( $_REQUEST['job'] != '' )
		{
			$job = $model_factory->getObject('co_ScheduledJob');
			$this->job_it = $job->getExact($_REQUEST['job']);
		}

		parent::Page();

		$object_it = $this->getObjectIt();

		if ( $this->needDisplayForm() )
		{
			$this->addInfoSection( new JobDescriptionSection() );
		}
	}

	function getObject()
	{
		$object = getFactory()->getObject('co_ScheduledJob');
		foreach( array('LastDuration','AverageDuration') as $attribute ) {
			$object->addAttributeGroup($attribute, 'nonbulk');
		}
		return $object;
	}
	
	function getTable()
	{
		if ( is_object($this->job_it) && $this->job_it->count() > 0 )
		{
			return new JobRunTable($this->job_it);
		}
		else
		{
			return new JobTable($this->getObject());
		}
	}

	function getForm()
	{
		if ( is_object($this->job_it) && $this->job_it->count() > 0 )
		{
			return null;
		}
		else
		{
			return new JobForm($this->getObject());
		}
	}
}
