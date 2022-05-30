<?php
include_once SERVER_ROOT_PATH."pm/classes/tasks/TaskViewModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/tasks/TaskViewModelCommonBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/RequestModelPageTableBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/widgets/BulkActionBuilderTasks.php";
include_once SERVER_ROOT_PATH."pm/views/reports/ReportTable.php";
include "TaskForm.php";
include "TaskBulkForm.php";
include "TaskTable.php";
include "WorkItemTable.php";

class TaskPlanningPage extends PMPage
{
    private $object = null;

 	function TaskPlanningPage()
 	{
        getSession()->addBuilder( new TaskViewModelCommonBuilder() );
 		getSession()->addBuilder( new BulkActionBuilderTasks() );
        getSession()->addBuilder( new RequestModelExtendedBuilder() );
        getSession()->addBuilder( new RequestModelPageTableBuilder() );

 		parent::__construct();
 		
 		if ( $this->needDisplayForm() )
 		{
            $this->addInfoSection( new PageSectionAttributes($this->getObject(),'source-issue',translate('Пожелание')) );
            $object_it = $this->getObjectIt();
            if ( is_object($object_it) && $object_it->count() > 0 )
            {
                $this->addInfoSection( new PageSectionComments($object_it, $this->getCommentObject()) );
                $this->addInfoSection( new StatableLifecycleSection( $object_it ) );
                $this->addInfoSection( new PMLastChangesSection ( $object_it ) );
                $this->addInfoSection(new NetworkSection($object_it));
            }
 		}
 	}

    function getCommentObject() {
        return new TaskComment();
    }

    function buildObject()
    {
        $object = getFactory()->getObject('Task');
        foreach(getSession()->getBuilders('TaskViewModelBuilder') as $builder ) {
           $builder->build($object);
        }
        $builder = new TaskModelExtendedBuilder();
        $builder->build($object);
        return $object;
    }

 	function getObject()
 	{
 	    if ( !$this->object ) $this->object = $this->buildObject();
 	    return $this->object;
 	}
 	
 	function getTableDefault()
 	{
		if ( in_array($this->getReportBase(), $this->getWorkItemReports()) ) {
		    $object = getFactory()->getObject('WorkItem');
            $builder = new TaskModelExtendedBuilder();
            $builder->build($object);
			return new WorkItemTable($object);
		}
		else {
			return new TaskTable($this->getObject());
		}
 	}
 	
 	function getTable() {
        return $this->getTableDefault();
 	}

	function needDisplayForm() {
		return $_REQUEST['view'] == 'import' || in_array($_REQUEST['mode'], array('bulk','group')) || parent::needDisplayForm();
	}
	
	function getBulkForm() {
		return new TaskBulkForm($this->getObject());
	}
	
 	function getEntityForm() {
        return new TaskForm($this->getObject());
 	}

	function getPageWidgets() {
		return array('tasksboard');
	}

	function getWorkItemReports() {
        return array(
            'mytasks',
            'nearesttasks',
            'tasksbyassignee',
            'assignedtasks',
            'assignedissues',
            'newtasks',
            'issuesmine',
            'watchedtasks',
            'resman/resourceload'
        );
    }
}