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

	function extendModel()
    {
        parent::extendModel();

        $this->getObject()->addAttribute('LastRun', '', translate('Предыдущий запуск'), true);
        $this->getObject()->addAttribute('LastDuration', 'INTEGER', text(1124), true);
        $this->getObject()->addAttribute('AverageDuration', 'INTEGER', text(1125), false);
    }

	function IsNeedToDisplay( $attr )
	{
		switch( $attr )
		{
			case 'Caption':
			case 'ClassName':
			case 'LastRun':
				return true;
		}

		return false;
	}

	function drawCell( $object_it, $attr )
	{
		switch ( $attr )
		{
			case 'LastRun':
				$run_it = getFactory()->getObject('co_JobRun')->getByRefArrayLatest(
				array('ScheduledJob' => $object_it->getId() ) );
				if ( $run_it->count() > 0 ) {
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

		if ( $actions[array_pop(array_keys($actions))]['name'] != '' ) $actions[] = array();
		
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
