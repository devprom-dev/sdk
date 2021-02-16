<?php

class JobRunList extends StaticPageList
{
	var $job_it;

	function JobRunList( $object, $job_it )
	{
		$this->job_it = $job_it;
		parent::__construct( $object );
	}

	function getIterator() {
		return $this->object->getRegistry()->Query(
		    array(
		        new FilterAttributePredicate('ScheduledJob', $this->job_it->getId()),
                new SortRecentClause()
            )
        );
	}

	function extendModel()
    {
        $this->getObject()->addAttribute('ExecutionTime', 'INTEGER', translate('Время выполнения'), true);
        $this->getObject()->addAttribute('Result', 'INTEGER', translate('Результат'), true);
        parent::extendModel();
    }

	function drawCell( $object_it, $attr )
	{
		switch ( $attr )
		{
			case 'ExecutionTime':
				echo SystemDateTime::convertToClientTime($object_it->get('RecordCreated'));
				break;

			case 'Result':
			    echo html_entity_decode($object_it->getWordsOnlyValue($object_it->get('Result'), 40), ENT_COMPAT | ENT_HTML401, APP_ENCODING);
			    break;
		}
	}

	function getGroupFields() {
		return array();
	}
}
