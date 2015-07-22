<?php

class JobList extends PageList
{
	function getRowColor( $object_it, $attr )
	{
		if ( $object_it->get('IsActive') == 'Y' )
		{
			return 'black';
		}
		else
		{
			return 'silver';
		}
	}

	function getColumns()
	{
		$this->object->addAttribute('Schedule', '', translate('Расписание'), true);
		$this->object->addAttribute('LastRun', '', translate('Предыдущий запуск'), true);

		$this->object->addAttribute('LastDuration', 'INTEGER', text(1124), true);
		$this->object->addAttribute('AverageDuration', 'INTEGER', text(1125), true);

		return parent::getColumns();
	}

	function IsNeedToDisplay( $attr )
	{
		switch( $attr )
		{
			case 'Caption':
			case 'ClassName':
			case 'Schedule':
			case 'LastRun':
				return true;
		}

		return false;
	}

	function drawCell( $object_it, $attr )
	{
		global $model_factory;

		switch ( $attr )
		{
			case 'Schedule':

				echo $object_it->get('Minutes').' &nbsp; ';
				echo $object_it->get('Hours').' &nbsp; ';
				echo $object_it->get('Days').' &nbsp; ';
				echo $object_it->get('WeekDays').' &nbsp; ';

				break;

			case 'LastRun':

				$jobrun = $model_factory->getObject('co_JobRun');

				$run_it = $jobrun->getByRefArrayLatest(
				array('ScheduledJob' => $object_it->getId() ) );

				if ( $run_it->count() > 0 )
				{
					echo $run_it->getDateTimeFormat('RecordCreated');
				}
					
				break;

			default:
				parent::drawCell( $object_it, $attr );
		}
	}

	function getItemActions( $column_name, $object_it )
	{
		$actions = parent::getItemActions( $column_name, $object_it );

		if ( $actions[count($actions)-1]['name'] != '' ) $actions[] = array();
		
		array_push( $actions, array( 
		    'url' => '/tasks/command.php?class=runjobs&job='.$object_it->getId().'&redirect=/admin/jobs.php', 
		    'name' => translate('Запустить') 
		));

		$actions[] = array();
		
		array_push( $actions, array( 
		    'url' => '?job='.$object_it->getId(),
		    'name' => translate('Результаты') 
		));

		return $actions;
	}

	function getGroupFields()
	{
		return array();
	}
}
