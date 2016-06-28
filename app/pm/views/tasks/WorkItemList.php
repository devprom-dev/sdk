<?php
include_once SERVER_ROOT_PATH."pm/views/time/FieldSpentTimeRequest.php";
include_once SERVER_ROOT_PATH."pm/views/issues/RequestForm.php";
include_once SERVER_ROOT_PATH."pm/methods/c_priority_methods.php";

class WorkItemList extends PMPageList
{
	private $priority_method = null;
    private $task = null;
    private $request = null;
    private $request_form = null;

	function buildRelatedDataCache()
	{
        $this->task = getFactory()->getObject('Task');
        $this->task->addAttribute('RecentComment', 'WYSIWYG', translate('Комментарии'), false);
        $this->task->addAttribute('Description', 'WYSIWYG', translate('Описание'), true, false, '', 15);
		$this->task->addAttribute('UID', 'INTEGER', 'UID', true, false, '', 0);

        $this->request = getFactory()->getObject('Request');
        $this->request->addAttribute('RecentComment', 'WYSIWYG', translate('Комментарии'), false);
		$this->request->addAttribute('UID', 'INTEGER', 'UID', true, false, '', 0);
        $this->request_form = new RequestForm($this->request);

		// cache priority method
		$has_access = getFactory()->getAccessPolicy()->can_modify($this->getObject())
				&& getFactory()->getAccessPolicy()->can_modify_attribute($this->getObject(), 'Priority');
		
		if ( $has_access ) {
			$this->priority_method = new ChangePriorityWebMethod(getFactory()->getObject('Priority')->getAll());
		}

        $this->getTable()->buildRelatedDataCache();
		$this->getTable()->cacheTraces('IssueTraces');
	}

    function getIt( $object_it )
    {
        $data = $object_it->getData();
        switch( $object_it->get('ObjectClass') ) {
            case 'Request':
                $data[$this->request->getIdAttribute()] = $object_it->getId();
                return $this->request->createCachedIterator(array($data));
            default:
                return $this->task->createCachedIterator(array($data));
        }
    }

    function getForm( $object_it )
    {
        if ( $object_it->object instanceof Request ) {
            return $this->request_form;
        }
        else {
            return parent::getForm($object_it);
        }
    }

	function getColumnFields()
	{
		$cols = parent::getColumnFields();
		$cols[] = 'OrderNum';
		return $cols;
	}

    function getGroupFields()
    {
        return array_values(array_intersect(
            array('Release', 'Priority', 'Assignee', 'Project', 'State', 'TaskType', 'PlannedRelease', 'DueDays', 'DueWeeks'),
            parent::getGroupFields()
        ));
    }

  	function IsNeedToSelect()
	{
		return false;
	}
	
	function IsNeedToSelectRow( $object_it )
	{
		return true;
	}

	function drawRefCell( $entity_it, $object_it, $attr )
	{
        $it = $this->getIt($object_it);

	    switch( $attr )
	    {
			case 'TaskType':
                if ( $object_it->get('TypeName') != '' ) {
                    echo $object_it->get('TypeName');
                }
                else {
                    echo $it->object->getDisplayName();
                }
				break;
	    	
			case 'Spent':
                if ( $object_it->get('ObjectClass') == 'Request' ) {
                    $field = new FieldSpentTimeRequest( $it );
                }
                else {
                    $field = new FieldSpentTimeTask( $it );
                }
                $field->setShortMode();
				$field->setEditMode( false );
				$field->setReadonly( !getFactory()->getAccessPolicy()->can_modify_attribute($it->object, 'Fact') );
				$field->render( $this->getTable()->getView() );
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
				$objects = preg_split('/,/', $object_it->get($attr));
				$uids = array();
				
				foreach( $objects as $object_info )
				{
					list($class, $id, $baseline) = preg_split('/:/',$object_info);
					if ( $class == '' ) continue;

					$uid = $this->getUidService();
					$uid->setBaseline($baseline);

					$ref_it = $this->getTable()->getTraces($class);
					$ref_it->moveToId($id);
					if ( $ref_it->getId() == '' ) continue;

					$uids[] = $uid->getUidIcon($ref_it);
				}
				echo join(' ',$uids);
				break;

            case 'RecentComment':
                parent::drawCell( $it, $attr );
                break;

			default:
				parent::drawCell( $it, $attr );
		}
	}
 	
	function drawGroup($group_field, $object_it)
	{
        $it = $this->getIt($object_it);

		switch ( $group_field )
		{
			case 'Assignee':
				$workload = $this->getTable()->getAssigneeUserWorkloadData();
				if ( count($workload) > 0 )
				{
						echo $this->getTable()->getView()->render('pm/UserWorkload.php', array (
								'user' => $it->getRef('Assignee')->getDisplayName(),
								'data' => $workload[$it->get($group_field)]
						));
				}				
				break;
				
			default:
				parent::drawGroup($group_field, $object_it);
		}

		$this->getTable()->drawGroup($group_field, $it);
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
			return 220;
		
		if ( $attr == 'OrderNum' )
			return '50';

		if ( $attr == 'TaskType' )
			return '95';
		
		return parent::getColumnWidth( $attr );
	}

	function getRenderParms()
	{
		$this->buildRelatedDataCache();
		
		return parent::getRenderParms();
	}
}