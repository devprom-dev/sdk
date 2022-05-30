<?php
include_once SERVER_ROOT_PATH."pm/views/time/FieldSpentTimeRequest.php";
include_once SERVER_ROOT_PATH."pm/views/issues/RequestForm.php";
include_once SERVER_ROOT_PATH."pm/views/tasks/TaskForm.php";

class WorkItemList extends PMPageList
{
    private $task = null;
    private $request = null;
    private $issue = null;
    private $request_form = null;
    private $task_form = null;

	function buildRelatedDataCache()
	{
        $this->task = getFactory()->getObject('Task');
        $this->task->addAttribute('Description', 'WYSIWYG', translate('Описание'), true, false, '', 15);
		$this->task->addAttribute('UID', 'INTEGER', 'UID', true, false, '', 0);
        $this->task_form = new TaskForm($this->task);

        $this->request = getFactory()->getObject('Request');
		$this->request->addAttribute('UID', 'INTEGER', 'UID', true, false, '', 0);
        $this->request_form = new RequestForm($this->request);

        if ( class_exists('Issue') ) {
            $this->issue = getFactory()->getObject('Issue');
            $this->issue->addAttribute('UID', 'INTEGER', 'UID', true, false, '', 0);
        }
	}

    function getItemClass($it) {
        return get_class($this->getIt($it)->object);
    }

    function getIt( $object_it )
    {
        $data = $object_it->getData();
        switch( $object_it->get('ObjectClass') ) {
            case 'Request':
                $data[$this->request->getIdAttribute()] = $object_it->getId();
                return $this->request->createCachedIterator(array($data));
            case 'Issue':
                if ( is_object($this->issue) ) {
                    $data[$this->issue->getIdAttribute()] = $object_it->getId();
                    return $this->issue->createCachedIterator(array($data));
                }
            case 'Task':
                return $this->task->createCachedIterator(array($data));
            default:
                if ( class_exists($object_it->get('ObjectClass')) ) {
                    $object = getFactory()->getObject($object_it->get('ObjectClass'));
                    $data[$object->getIdAttribute()] = $object_it->getId();
                    return $object->createCachedIterator(array($data));
                }
                return $this->task->createCachedIterator(array($data));
        }
    }

    function getForm( $object_it )
    {
        if ( $object_it->object->getEntityRefName() == 'pm_ChangeRequest' ) {
            return $this->request_form;
        }
        elseif ( $object_it->object instanceof Task ) {
            return $this->task_form;
        }
        else {
            return new PMPageForm($object_it->object);
        }
    }

    function getGroupFields()
    {
        return array_values(array_intersect(
            array('Release', 'Priority', 'Assignee', 'Project', 'State', 'TaskType', 'PlannedRelease', 'DueWeeks'),
            parent::getGroupFields()
        ));
    }

  	function IsNeedToSelect()
	{
		return false;
	}
	
	function drawRefCell( $entity_it, $object_it, $attr )
	{
        $it = $this->getIt($object_it);

	    switch( $attr )
	    {
			case 'Spent':
                if ( $object_it->get('ObjectClass') == 'Task' ) {
                    $field = new FieldSpentTimeTask( $it );
                }
                else {
                    $field = new FieldSpentTimeRequest( $it );
                }
                $field->setShortMode();
				$field->setEditMode( false );
				$field->setReadonly( !getFactory()->getAccessPolicy()->can_modify_attribute($it->object, 'Fact') );
				$field->render( $this->getRenderView() );
				break;

            case 'Release':
            case 'PlannedRelease':
                parent::drawRefCell( $entity_it, $object_it, $attr );
                break;

	        default:
	            parent::drawRefCell( $entity_it, $it, $attr );
	    }
	}
	
	function drawCell( $object_it, $attr )
	{
        $it = $this->getIt($object_it);

		switch($attr)
		{
            case 'IssueTraces':
                $this->getTable()->drawCell( $object_it, $attr );
				break;

            case 'RecentComment':
                parent::drawCell( $it, $attr );
                break;

            case 'DueDate':
                $deadline_alert = $object_it->get('DueWeeks') < 4 && $object_it->get('DueDate') != '';
                if ( $deadline_alert ) {
                    echo '<span class="date-label label '.($object_it->get('DueWeeks') < 3 ? 'label-important' : 'label-warning').'">';
                    parent::drawCell($object_it, $attr);
                    echo '</span>';
                } else {
                    parent::drawCell($object_it, $attr);
                }
                break;

			default:
				parent::drawCell( $it, $attr );
		}
	}
 	
	function drawGroup($group_field, $object_it)
	{
		switch ( $group_field )
		{
			default:
				parent::drawGroup($group_field, $object_it);
		}

		$this->getTable()->drawGroup($group_field, $object_it);
	}

    function getActions( $object_it )
    {
        return parent::getActions($this->getIt($object_it));
    }

 	function getColumnWidth( $attr ) 
	{
		if ( $attr == 'Priority' )
			return 80;

		if ( $attr == 'State' )
			return 80;

        if ( $attr == 'DueDate' )
            return 70;

		if ( $attr == 'Spent' )
			return 190;
		
		if ( $attr == 'OrderNum' )
			return '50';

		if ( $attr == 'TaskType' )
			return '95';
		
		return parent::getColumnWidth( $attr );
	}

    function getBulkAttributes() {
        return array();

    }
	function getRenderParms()
	{
		$this->buildRelatedDataCache();
		
		return parent::getRenderParms();
	}
}