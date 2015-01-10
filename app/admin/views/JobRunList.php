<?php

class JobRunList extends StaticPageList
{
	var $job_it;

	function JobRunList( $object, $job_it )
	{
		$this->job_it = $job_it;
		parent::StaticPageList( $object );
	}

	function getIterator()
	{
		$this->object->defaultsort = 'RecordCreated DESC';
		return $this->object->getByRefArray(
		array( 'ScheduledJob' => $this->job_it->getId() ) );
	}

	function IsNeedToDisplay( $attr )
	{
		switch ( $attr )
		{
			case 'ExecutionTime':
			case 'Result':
				return true;
		}

		return false;
	}

	function getColumns()
	{
		$this->object->addAttribute('ExecutionTime', 'INTEGER', translate('Время выполнения'), true);
		$this->object->addAttribute('Result', 'INTEGER', translate('Результат'), true);

		return parent::getColumns();
	}

	function drawCell( $object_it, $attr )
	{
		global $model_factory;

		switch ( $attr )
		{
			case 'ExecutionTime':
				echo SystemDateTime::convertToClientTime($object_it->get('RecordCreated'));
				break;

			case 'Result':

			    echo html_entity_decode($object_it->getWordsOnlyValue($object_it->get('Result'), 40), ENT_COMPAT | ENT_HTML401, 'cp1251');

			    break;
		}
	}

	function getGroupFields()
	{
		return array();
	}
}
