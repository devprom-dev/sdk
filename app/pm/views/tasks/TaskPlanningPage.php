<?php
include_once SERVER_ROOT_PATH."pm/classes/tasks/TaskViewModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/tasks/TaskViewModelCommonBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/RequestModelPageTableBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/widgets/BulkActionBuilderTasks.php";
include_once SERVER_ROOT_PATH.'pm/views/import/ImportXmlForm.php';
include_once SERVER_ROOT_PATH."pm/views/reports/ReportTable.php";

include "TaskForm.php";
include "TaskBulkForm.php";
include "TaskTable.php";
include "WorkItemTable.php";

class TaskPlanningPage extends PMPage
{
 	function TaskPlanningPage()
 	{
 		getSession()->addBuilder( new TaskViewModelCommonBuilder() );
 		getSession()->addBuilder( new BulkActionBuilderTasks() );
        getSession()->addBuilder( new RequestModelExtendedBuilder() );
        getSession()->addBuilder( new RequestModelPageTableBuilder() );

 		parent::PMPage();
 		
 		if ( $this->needDisplayForm() )
 		{
            $this->addInfoSection( new PageSectionAttributes($this->getObject(),'deadlines',translate('Сроки')) );
            $this->addInfoSection( new PageSectionAttributes($this->getObject(),'source-issue',translate('Пожелание')) );
            $this->addInfoSection( new PageSectionAttributes($this->getObject(),'additional',translate('Дополнительно')) );
            $this->addInfoSection( new PageSectionAttributes($this->getObject(),'trace',translate('Трассировки')) );

            $object_it = $this->getObjectIt();
            if ( is_object($object_it) && $object_it->count() > 0 )
            {
                $this->addInfoSection( new PageSectionComments($object_it) );
                $this->addInfoSection( new StatableLifecycleSection( $object_it ) );
                $this->addInfoSection( new PMLastChangesSection ( $object_it ) );
            }
 		}
 		else if ( $_REQUEST['mode'] != 'bulk' ) {
 		    if ( $_REQUEST['view'] == 'board' ) {
				$this->addInfoSection( new FullScreenSection() );
			}
			$this->addInfoSection(new DetailsInfoSection());
 		}
 	}
 	
 	function getObject()
 	{
		if ( in_array($this->getSessionReportName(), $this->getWorkItemReports()) ) {
			$object = getFactory()->getObject('WorkItem');
		} else {
			$object = getFactory()->getObject('Task');
		}

	    foreach(getSession()->getBuilders('TaskViewModelBuilder') as $builder ) {
    		$builder->build($object);
    	}

		$builder = new TaskModelExtendedBuilder();
		$builder->build($object);

 		return $object;
 	}
 	
 	function getTableDefault()
 	{
		if ( in_array($this->getSessionReportName(), $this->getWorkItemReports()) ) {
			return new WorkItemTable($this->getObject());
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
	
 	function getForm() 
 	{
		if ($_REQUEST['view'] == 'import') {
			return new ImportXmlForm($this->getObject());
		}
		else {
			return new TaskForm($this->getObject());
		}
 	}

	function getSessionReportName()
	{
		$report_it = getFactory()->getObject('PMReport')->getExact($_REQUEST['report']);
		if ( is_numeric($report_it->getId()) ) {
			return $report_it->get('Report');
		}
		return $report_it->getId();
	}

	function getPageWidgets()
	{
		return array('tasksboard');
	}

	function isDetailsActive() {
		return $this->getReportBase() != 'mytasks';
	}

	function getWorkItemReports()
    {
        return array(
            'mytasks',
            'nearesttasks',
            'assignedtasks',
            'newtasks',
            'issuesmine',
            'watchedtasks',
            'workitemchart'
        );
    }
}