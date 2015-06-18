<?php

include ('JobRunList.php');

class JobRunTable extends StaticPageTable
{
	var $job_it;

	function __construct( $job_it )
	{
		global $model_factory;
		
		$this->job_it = $job_it;
			
		parent::__construct($model_factory->getObject('co_JobRun'));
	}

	function getList()
	{
		return new JobRunList( $this->object, $this->job_it );
	}

	function getCaption()
	{
		return translate('Результаты выполнения задания').': '.$this->job_it->getDisplayName();
	}

	function getFilterActions()
	{
		return array();
	}
}
